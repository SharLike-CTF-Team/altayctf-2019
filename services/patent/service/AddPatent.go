package main

import (
	"database/sql"
	"text/template"
	"fmt"
	_ "github.com/lib/pq"
	"encoding/json"
	"log"
	"net/http"
	"net/url"
	"strconv"
)

func AddPatentHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	params := url.Values{}
	var paramsData PatentParams
	tmpl, err := template.ParseFiles("templates/patent.html")
	if err != nil {
		log.Println(err)
	}
	if r.Method == "POST" {
		r.ParseForm()
		params = r.Form

		log.Println("ObjectName:", params.Get("ObjectName"))
		log.Println("ObjectDescription:", params.Get("ObjectDescription"))
		log.Println("Owner:", params.Get("Owner"))
		log.Println("CVV:", params.Get("CVV"))
		log.Println("CardNumber:", params.Get("CardNumber"))
		log.Println("Mon:", params.Get("Mon"))
		log.Println("Year:", params.Get("Year"))


		if paramsData.ObjectName = params.Get("ObjectName"); paramsData.ObjectName == "" {
			paramsData.Error = "Not set ObjectName."
			log.Println(paramsData.Error)
			tmpl.Execute(w, paramsData)
			return
		}
		if paramsData.ObjectDescription = params.Get("ObjectDescription"); paramsData.ObjectDescription == "" {
			paramsData.Error = "Not set ObjectDescription."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}
		if paramsData.Owner = params.Get("Owner"); paramsData.Owner == "" {
			paramsData.Error = "Not set Owner."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}
		if paramsData.CVV, _ = strconv.Atoi(params.Get("CVV")); paramsData.CVV == 0 {
			paramsData.Error = "Not set CVV."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}
		if paramsData.CardNumber = params.Get("CardNumber"); paramsData.CardNumber == "" {
			paramsData.Error = "Not set CardNumber."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}
		if paramsData.Mon, _ = strconv.Atoi(params.Get("Mon")); paramsData.Mon == 0 {
			paramsData.Error = "Not set Mon."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}
		if paramsData.Year, _ = strconv.Atoi(params.Get("Year")); paramsData.Year == 0 {
			paramsData.Error = "Not set Year."
			tmpl.Execute(w, paramsData)
			log.Println(paramsData.Error)
			return
		}

		resp, err := http.Get("http://127.0.0.1/get/id/?sequence_name=card_seq")
		if err != nil {
			fmt.Println(err)
			return
		}
		defer resp.Body.Close()

		user_id := GetUserId(r)
		log.Println("User_id:", user_id)

		bs := make([]byte, 1014)
		n, err := resp.Body.Read(bs)
		var res SequenceInfo
		err = json.Unmarshal(bs[:n], &res)
		log.Println("SEQQQ bs:", string(bs[:n]))
		log.Println("SEQQQ RES:", res.Name, res.NextId)
		paramsData.CardId = res.NextId

		var card_id int
		card_id = res.NextId
		log.Println("Card_id:", card_id)

		_, err = db.Exec(`
			INSERT INTO card (row, cardNumber, owner, mon, year, cvv)
			VALUES ($1,$2,$3,$4,$5,$6)`,
			card_id, paramsData.CardNumber, paramsData.Owner, paramsData.Mon, paramsData.Year, paramsData.CVV,
		)
		if err != nil {
			log.Println("Error INSERT card:", err)
		}

		_, err = db.Exec(`
			INSERT INTO object_info (autor_id, card_id, name, description)
			VALUES ($1,$2,$3,$4)`,
			user_id, card_id, paramsData.ObjectName, paramsData.ObjectDescription,
		)
		if err != nil {
			log.Println("Error INSERT object_info:", err)
		}
		paramsData.Error = ""

		tmpl.Execute(w, paramsData)
	} else {
		tmpl.Execute(w, paramsData)
	}
}
