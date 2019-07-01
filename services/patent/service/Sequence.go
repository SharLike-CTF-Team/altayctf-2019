package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	_ "github.com/lib/pq"
	"io"
	"log"
	"net/http"
	"net/url"
	"strings"
)

type SequenceInfo struct {
	Name	string	`json:"name"`
	NextId	int		`json:"nextid"`
}

func getId(name string, db *sql.DB) (int, error) {
	var res int
	query := fmt.Sprintf("SELECT nextval('%s')", strings.Replace(name, ";", "", -1))
	fmt.Println("QUERY:", query)
	row := db.QueryRow(query)
	err := row.Scan(&res)
	fmt.Println("RES SEQ:", res, err)

	return res, err
}

func getSecuenceNext(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	params := url.Values{}

	if r.Method == "POST" {
		r.ParseForm()
		params = r.Form
	}
	if r.Method == "GET" {
		params = r.URL.Query()
	}

	w.Header().Set("Content-type", "application/json")

	sName := params.Get("sequence_name")
	sId, err := getId(sName, db)
	if err != nil {
		log.Println("Error get seguense:", sName, sId, err)
	}
	sequence := SequenceInfo{sName, sId}
	body, err := json.Marshal(sequence)
	if err != nil {
		log.Println("Sequence Error: ", err)
	}

	io.WriteString(w, string(body))
}
