package main

import (
	"database/sql"
	"text/template"
	_ "github.com/lib/pq"
	"log"
	"net/http"
	"net/url"
	"strconv"
)

func CardInfoHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	params := url.Values{}
	var paramsData CardInfoParams
	paramsData.View = 1

	tmpl, err := template.ParseFiles("templates/cardinfo.html")
	if err != nil {
		log.Println(err)
	}
	if r.Method == "POST" {
		r.ParseForm()
		params = r.Form
	} else if r.Method == "GET" {
		params = r.URL.Query()
	}
	paramsData.CardId, _ = strconv.Atoi(params.Get("CardId"))
	paramsData.SecretKey = params.Get("SecretKey")

	log.Println("CardId:", params.Get("CardId"))
	log.Println("SecretKey:", params.Get("SecretKey"))
	if len(paramsData.SecretKey) > 0 {
		paramsData.View = 1
		query := `
			SELECT c.cardNumber, c.owner, c.mon, c.year, c.cvv
			FROM users u
  				JOIN object_info oi ON oi.autor_id = u.row
  				JOIN card c ON c.row = oi.card_id
    				AND card_id = $1
			WHERE u.secretkey = $2
		`
		err := db.
			QueryRow(query, paramsData.CardId, paramsData.SecretKey).
			Scan(&paramsData.CardNumber, &paramsData.Owner, &paramsData.Mon, &paramsData.Year, &paramsData.CVV)
		if err != nil {
			log.Println("ERROR SELECT CardInfo:", err)
		}
	} else {
		paramsData.View = 0
	}

	tmpl.Execute(w, paramsData)
}
