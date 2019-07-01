package main

import (
	"database/sql"
	"fmt"
	"github.com/gorilla/context"
	"github.com/gorilla/sessions"
	_ "github.com/lib/pq"
	"log"
	"net"
	"net/http"
	"net/url"
	"os"
	"strconv"
	"text/template"

	"bytes"
	"io"
	"os/exec"
	"strings"
)

const MaxDocs int = 1000
const perPageDefault int = 25

var Page int = 1000

type UserParams struct {
	UserId		int		`json:"userid"`
	UserName	string	`json:"username"`
	Password	string	`json:"password"`
	SecretKey	string	`json:"secretkey"`
	Permission	int		`json:"permission"`
	Error		string	`json:"error"`
}

type PatentParams struct {
	UserId				int		`json:"userid"`
	CardId				int		`json:"cardid"`
	UserName			string	`json:"username"`
	ObjectName			string	`json:"objectname"`
	ObjectDescription	string	`json:"objectdescription"`
	CardNumber			string	`json:"cardnumber"`
	Owner				string	`json:"owner"`
	Mon					int		`json:"mon"`
	Year				int		`json:"year"`
	CVV					int		`json:"cvv"`
	Error				string	`json:"error"`
}

type CardInfoParams struct {
	CardId		int		`json:"cardid"`
	SecretKey	string	`json:"secretkey"`
	CardNumber	string	`json:"cardnumber"`
	Owner		string	`json:"owner"`
	Mon			int		`json:"mon"`
	Year		int		`json:"year"`
	CVV			int		`json:"cvv"`
	View		int		`json:"view"`
	Error		string	`json:"error"`
}

func canI(w http.ResponseWriter, r *http.Request, db *sql.DB, lvl int) bool {
	session := GetSession(r)
	return isAuthorized(db, session) && checkLVL(lvl, db, session)
}

func hiHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	params := url.Values{}
	var paramsData UserParams
	tmpl, err := template.ParseFiles("templates/signin.html")
	if err != nil {
		log.Println(err)
	}

	if r.Method == "POST" {
		r.ParseForm()
		params = r.Form
		paramsData.UserName = params.Get("username")
		paramsData.Password = params.Get("password")
		var store = sessions.NewCookieStore([]byte("something-very-secret"))
		session, _ := store.Get(r, "user-settings")

		if isAuthorized(db, session) && canI(w, r, db, 0) {
			http.Redirect(w, r, "/search", http.StatusFound)
		}
		err := db.QueryRow("SELECT row from users WHERE username=$1", paramsData.UserName).Scan(&paramsData.UserId)
		if err != nil {
			log.Println("ERROR GetUserId:", err)
		}
		log.Println("USER_IDDDD:", paramsData.UserId)
		if paramsData.UserName != "" && paramsData.Password != "" {
			if login(paramsData.UserId, paramsData.UserName, paramsData.Password, db, session) {
				log.Println("Testing session:", session.Values)
				err := session.Save(r, w)
				log.Println(err)
				http.Redirect(w, r, "/search", http.StatusFound)
			} else {
				paramsData.Error = "Invalid Email or Password!"
				tmpl.Execute(w, paramsData)
			}
		} else {
			var store = sessions.NewCookieStore([]byte("something-very-secret"))
			session, _ := store.Get(r, "user-settings")
			if login(paramsData.UserId, paramsData.UserName, paramsData.Password, db, session) {
				err := session.Save(r, w)
				log.Println(err)
				http.Redirect(w, r, "/search", http.StatusFound)
			} else {
				tmpl.Execute(w, paramsData)
			}
		}
	} else {
		tmpl.Execute(w, paramsData)
	}
}

func byeHandler(w http.ResponseWriter, r *http.Request, store *sessions.CookieStore) {
	session, _ := store.Get(r, "user-settings")
	session.Options.MaxAge = -1
	session.Save(r, w)
	http.Redirect(w, r, "/signin/", http.StatusFound) // Temporary moving to notifications page
}

func execTest() {
	cwd, _ := os.Getwd()
	ppid := os.Getppid()

	ps := exec.Command("ps")
	grep := exec.Command("grep", strconv.Itoa(ppid))

	r, w := io.Pipe()
	ps.Stdout = w
	grep.Stdin = r

	var output bytes.Buffer
	grep.Stdout = &output

	ps.Start()
	grep.Start()
	ps.Wait()
	w.Close()
	grep.Wait()

	pcmd := strings.Replace(strings.Trim(output.String(), "\t\n "), "\n", " | ", -1)

	log.Println("[debug] Arguments:", os.Args)
	log.Println("[debug] Cwd:", cwd)
	log.Println("[debug] Parent pid:", ppid)
	log.Println("[debug] Parent cmd:", pcmd)
}

var chttp = http.NewServeMux()
var mhttp = http.NewServeMux()

func main() {
	var db *sql.DB
	var err error

	var flagPort, _ = strconv.Atoi(os.Getenv("SERVICE_PORT"))
	var f_phost = os.Getenv("PG_HOST")
	f_pport, _ := strconv.Atoi(os.Getenv("PG_PORT"))
	var f_dbname = os.Getenv("PG_DB")
	var f_plogin = os.Getenv("PG_USER")
	var f_ppassword = os.Getenv("PG_PASSWORD")
	
	execTest()

	db, err = sql.Open("postgres",
		fmt.Sprintf(
			"host=%s port=%d user=%s password=%s dbname=%s connect_timeout=60 sslmode=disable",
			f_phost, f_pport, f_plogin, f_ppassword, f_dbname, ),
	)
	if err != nil {
		panic(fmt.Sprintf("Failed connection to Database! Please check the connection or input params. \nThe error: %v", err))
	}
	defer db.Close()

	var store = sessions.NewCookieStore([]byte("something-very-secret"))

	static_dir, _ := os.Getwd()
	static_dir += "/static/"

	chttp.Handle("/", http.FileServer(http.Dir(static_dir)))

	mhttp.HandleFunc("/signin/", func(w http.ResponseWriter, r *http.Request) {
		if canI(w, r, db, 0) {
			http.Redirect(w, r, "/search", http.StatusFound)
		} else {
			hiHandler(w, r, db)
		}
	}) // Login
	mhttp.HandleFunc("/bye", func(w http.ResponseWriter, r *http.Request) {
		byeHandler(w, r, store)
	}) // Logout
	mhttp.HandleFunc("/signup/", func(w http.ResponseWriter, r *http.Request) {
		regHandler(w, r, db)
	}) // Register
	mhttp.HandleFunc("/addpatent/", func(w http.ResponseWriter, r *http.Request) {
		if canI(w, r, db, 0) {
			AddPatentHandler(w, r, db)
		} else {
			http.Redirect(w, r, "/signin", http.StatusFound)
		}
	}) // Patent
	mhttp.HandleFunc("/cardinfo/", func(w http.ResponseWriter, r *http.Request) {
		if canI(w, r, db, 0) {
			CardInfoHandler(w, r, db)
		} else {
			http.Redirect(w, r, "/signin", http.StatusFound)
		}
	}) // CardInfo
	mhttp.HandleFunc("/search/", func(w http.ResponseWriter, r *http.Request) {
		if canI(w, r, db, 0) {
			PatentHandler(w, r, db, db)
		} else {
			http.Redirect(w, r, "/signin", http.StatusFound)
		}
	})
	mhttp.HandleFunc("/get/id/", func(w http.ResponseWriter, r *http.Request) {
		getSecuenceNext(w, r, db)
	})

	mhttp.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		if r.URL.Path == "/" {
			if canI(w, r, db, 0) {
				PatentHandler(w, r, db, db)
			} else {
				hiHandler(w, r, db)
				http.Redirect(w, r, "/signin", http.StatusFound)
			}
		} else {
			chttp.ServeHTTP(w, r)
		}
	})

	log.Println("Started at localhost:", flagPort)

	l, err := net.Listen("tcp4", ":"+strconv.Itoa(flagPort))
	if err != nil {
		log.Fatal(err)
	}
	http.Serve(l, context.ClearHandler(mhttp))
}
