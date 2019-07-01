package main

import (
	"database/sql"
	"text/template"
	"github.com/gorilla/sessions"
	_ "github.com/lib/pq"
	"log"
	"net/http"
	"net/url"
)

func regHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	params := url.Values{}
	var paramsData UserParams
	tmpl, err := template.ParseFiles("templates/signup.html")
	if err != nil {
		log.Println(err)
	}

	if r.Method == "POST" {
		r.ParseForm()
		params = r.Form

		log.Println("username:", params.Get("username"))
		log.Println("password:", params.Get("password"))
		log.Println("secretkey:", params.Get("secretkey"))

		if paramsData.UserName = params.Get("username"); paramsData.UserName == "" {
			paramsData.Error = "Not set username."
			log.Println(paramsData.Error)
			tmpl.Execute(w, paramsData)
			return
		}
		if paramsData.Password = params.Get("password"); paramsData.Password == "" {
			paramsData.Error = "Not set password."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}
		if paramsData.SecretKey = params.Get("secretkey"); paramsData.Password == "" {
			paramsData.Error = "Not set secretkey."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}
		if user_id, ok := signUp(paramsData.UserName, paramsData.Password, paramsData.SecretKey, permissionDemo, db); !ok {
			paramsData.Error = "An error occurred during registration. Try again."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		} else {
			var store = sessions.NewCookieStore([]byte("something-very-secret"))
			session, _ := store.Get(r, "user-settings")
			if login(user_id, paramsData.UserName, paramsData.Password, db, session) {
				err := session.Save(r, w)
				log.Println(err)
				http.Redirect(w, r, "/search", http.StatusFound)
			}
		}
	} else {
		tmpl.Execute(w, paramsData)
	}
}
