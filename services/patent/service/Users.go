package main

import (
	"crypto/md5"
	"database/sql"
	"encoding/hex"
	"fmt"
	"github.com/gorilla/sessions"
	_ "github.com/lib/pq"
	"log"
	"net/http"
	"strings"
	"time"
)

const permissionDemo int = 0

func pwdCheck(db *sql.DB, username, password string) bool {
	var controlSum string
	db.QueryRow("SELECT password from users WHERE username=$1", username).Scan(&controlSum)
	log.Println("controlSum: ", controlSum, "password: ", password)
	if controlSum == password {
		return true
	}
	return false
}

func isAuthorized(db *sql.DB, session *sessions.Session) bool {
	if v, ok := session.Values["username"]; ok {
		email, _ := v.(string)
		if v, ok := session.Values["pwd"]; ok {
			pwd, _ := v.(string)
			return pwdCheck(db, email, pwd)
		} else {
			return false
		}
	} else {
		return false
	}
}
func accessLVL(username string, lvl int, db *sql.DB) (bool, int) {
	var resLVL int
	err := db.QueryRow("SELECT permission from users WHERE username=$1", username).Scan(&resLVL)
	log.Println("AccessLVL for ", username, " is ", resLVL)
	if err != nil {
		log.Println(err)
	}
	return resLVL >= lvl, resLVL
}

func checkLVL(lvl int, db *sql.DB, session *sessions.Session) bool {
	if v, ok := session.Values["username"]; ok {
		s, _ := v.(string)
		so, _ := accessLVL(s, lvl, db)
		return so
	} else {
		return false
	}

}

func login(user_id int, username, password string, db *sql.DB, session *sessions.Session) bool {
	fpassword := password
	h := md5.New()
	h.Write([]byte(password))

	password = hex.EncodeToString(h.Sum(nil))
	if !pwdCheck(db, username, password) {
		log.Println("Login Error: ", username, password)
		return false
	}
	timestamp := time.Now().UTC()
	_, err := db.Exec("INSERT INTO sessions (username, time) VALUES ($1,$2,to_timestamp(translate($3, 'T', ' '), 'YYYY-MM-DD HH24:MI:SS'))", username, timestamp)
	if err != nil {
		log.Println("Login error: ", err)
	}
	_, session.Values["lvl"] = accessLVL(username, 0, db)
	if session.Values["lvl"].(int) < 0 {
		return false
	}
	session.Values["userid"] = user_id
	session.Values["username"] = username
	session.Values["pwd"] = password
	session.Values["f"] = fpassword
	log.Println("Saving to session:", password, fpassword)
	session.Values["timestamp"] = fmt.Sprintf("%d-%02d-%02d %02d:%02d:%02d\n",
		timestamp.Year(), timestamp.Month(), timestamp.Day(),
		timestamp.Hour(), timestamp.Minute(), timestamp.Second())
	return true
}

func logout(session *sessions.CookieStore) {
	session.MaxAge(-1)
}

func signUp(username, password, secretkey string, permission int, db *sql.DB) (int, bool) {
	var user_id int
	db.QueryRow("SELECT row FROM users where username = $1", username).Scan(&user_id)
	if user_id != 0 {
		log.Println("User with username ", username, "already exists")
		return user_id, false
	}
	h := md5.New()
	h.Write([]byte(password))
	password = hex.EncodeToString(h.Sum(nil))
	err := db.QueryRow(`
		INSERT INTO users (username, password, secretkey, permission)
		VALUES ($1,$2,$3,$4)
		RETURNING row
		`,
		strings.TrimSpace(username), password, secretkey, permission,
	).Scan(&user_id)
	if err != nil {
		log.Println("Error INSERT users:", err)
	}

	return user_id, true
}

func GetSession(r *http.Request) *sessions.Session {
	var store = sessions.NewCookieStore([]byte("something-very-secret"))
	session, err := store.Get(r, "user-settings")
	if err != nil {
		log.Println("GetSession ERROR: ", err)
	}
	return session
}

func GetUserName(r *http.Request) string {
	session := GetSession(r)
	if v, ok := session.Values["username"]; ok {
		str, _ := v.(string)
		return str
	} else {
		return ""
	}
}

func GetUserId(r *http.Request) int {
	session := GetSession(r)
	if v, ok := session.Values["userid"]; ok {
		log.Println("GetUserId:", v)
		id, _ := v.(int)
		return id
	} else {
		log.Println("GetUserId error:", v, ok)
		return 0
	}
}

