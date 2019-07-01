package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	_ "github.com/lib/pq"
	"github.com/yunge/sphinx"
	"html/template"
	"log"
	"math"
	"net/http"
	"net/url"
	"strconv"
)

type Patent struct {
	Row					int		`json:"row"`
	UserName			string	`json:"username"`
	ObjectName			string	`json:"objectname"`
	ObjectDescription	string	`json:"objectdescription"`
	CardId				int		`json:"cardid"`
	Query				string	`json:"query"`
	Error				string	`json:"error"`
}

type PatentResult struct {
	UserName			string		`json:"email"`
	ObjectName			string		`json:"data"`
	ObjectDescription	string		`json:"total"`

	Data				[]Patent	`json:"data"`
	Total				int			`json:"total"`
	Count				int			`json:"count"`
	Query				string		`json:"query"`
	FilterPage			int			`json:"PageNum"`
	PerPage				int			`json:"perPage"`
	IsSearch			bool		`json:"isSearch"`
	Lvl					int			`json:"lvl"`
	Error				string		`json:"error"`
}

func (p *PatentResult) isNotEmpty() bool {
	return p.Count != 0
}

func omitempty(t string, v interface{}) string {
	switch v.(type) {
	case string:
		if v != "" {
			return fmt.Sprintf("&"+t+"=%s", url.QueryEscape(v.(string)))
		} else {
			return ""
		}
	case float64:
		if v.(float64) == 0 {
			return ""
		}
		return fmt.Sprintf("&"+t+"=%2f", v.(float64))
	case float32:
		if v.(float32) == 0 {
			return ""
		}
		return fmt.Sprintf("&"+t+"=%2f", v.(float32))
	case int:
		if v.(int) == 0 {
			return ""
		}
		return fmt.Sprintf("&"+t+"=%d", v.(int))
	case bool:
		return fmt.Sprintf("&"+t+"=%t", v.(bool))
	case []string:
		a := v.([]string)
		if len(a) != 0 {
			return fmt.Sprintf("&"+t+"=%s", urlEscapeStringArray(a, t))
		} else {
			return ""
		}
	default:
		return ""
	}
}

func urlEscapeStringArray(array []string, prefix string) string {
	var s string
	for i, v := range array {
		if i != 0 {
			s += "&" + prefix + "=" + url.QueryEscape(v)
		} else {
			s += url.QueryEscape(v)
		}
	}
	return s
}

func (r *PatentResult) genURL(xls bool, page int) string {
	s := "/search/?query=" + url.QueryEscape(r.Query)
	if page > 0 {
		s += omitempty("filterPage", page)
	}
	s += omitempty("perPage", r.PerPage)

	return s
}
func (p PatentResult) Pages() template.HTML {
	if p.PerPage == 0 {
		p.PerPage = perPageDefault
	}

	log.Println("PAGES():", p.PerPage, p.FilterPage)
	totalPage := int(math.Ceil(float64(p.Total) / float64(p.PerPage)))

	startPage := p.FilterPage - 1;
	if startPage < 1 { startPage = 1 }
	endPage := startPage + 4
	if endPage > totalPage {
		endPage = totalPage
		startPage = endPage - 4
		if startPage < 1 { startPage = 1}
	}
	prevPage := p.FilterPage - 1
	if prevPage < 1 { prevPage = 1 }
	nextPage := p.FilterPage + 1
	if nextPage > totalPage { nextPage = totalPage }

	var templateD string
	if p.Total > p.PerPage {
		templateD += fmt.Sprintf(`
			<div class="row justify-content-md-center">
				<nav>
					<ul class="pagination">
						<li class="page-item"><a class="page-link" href="%s"><<</a></li>
						<li class="page-item"><a class="page-link" href="%s"><</a></li>`, p.genURL(false, 0), p.genURL(false, prevPage))
		for i := startPage; i <= endPage; i++ {
			if i == p.FilterPage {
				templateD += fmt.Sprintf(`
						<li class="page-item"><a class="page-link" style="color: #000;" href="%s">%d</a></li>`, p.genURL(false, i), i)
			} else {
				templateD += fmt.Sprintf(`
						<li class="page-item"><a class="page-link" href="%s">%d</a></li>`, p.genURL(false, i), i)
			}
		}
		templateD += fmt.Sprintf(`
						<li class="page-item"><a class="page-link" href="%s">></a></li>
						<li class="page-item"><a class="page-link" href="%s">>></a></li>
					</ul>
				</nav>
			<div class="row justify-content-md-center">`, p.genURL(false, nextPage), p.genURL(false, totalPage))
	}

	return template.HTML(templateD)
}

type PatentFilters struct {
	filterPage	int
	perPage		int
}

func (f *PatentFilters) setFilters(params url.Values) {
	f.filterPage, _ =  strconv.Atoi(params.Get("filterPage"))
	f.perPage, _ = strconv.Atoi(params.Get("perPage"))
	if f.filterPage != 0 {
		f.filterPage -= 1
	}
	if f.perPage > 1000 {
		f.perPage = 1000
	}
	if f.perPage == 0 {
		f.perPage = perPageDefault
	}
}

func (result *PatentResult) setMeta(query string, f PatentFilters) {
	result.Query = query

	result.PerPage = int(f.perPage)
}

func PatentHandler(w http.ResponseWriter, r *http.Request, db, dbs *sql.DB) {
	w.Header().Set("Access-Control-Allow-Origin", "*")
	_, lvl := accessLVL(GetUserName(r), 0, dbs)
	params := url.Values{}
	if r.Method == "POST" {
		r.ParseForm()
		params = r.Form
	}
	if r.Method == "GET" {
		params = r.URL.Query()
	}
	log.Println("PARAMS:", params)
	var query string
	var result PatentResult
	result.Lvl = lvl
	var filters PatentFilters
	if len(params) != 0 {
		log.Println("Params Debug:", params)
		query = params.Get("query")
		filters.setFilters(params)

		result = PatentSearch(db, query, filters)
		result.IsSearch = true
		_, result.Lvl = accessLVL(GetUserName(r), 0, dbs)

		if r.Method == "POST" {
			w.Header().Set("Content-type", "application/json")
			t, _ := json.Marshal(&result)

			fmt.Fprintf(w, "%s", string(t))
		}
		if r.Method == "GET" {
			tmpl, err := template.ParseFiles("templates/search.html")
			tmpl.Execute(w, result)

			log.Println("TEST LVL:", result.Lvl)
			if err != nil {
				log.Println(err)
			}
		}
	} else {
		if r.Method == "POST" {
			w.Header().Set("Content-type", "application/json")
			t, _ := json.Marshal(&result)
			fmt.Fprintf(w, "%s", string(t))
		}
		if r.Method == "GET" {
			tmpl, err := template.ParseFiles("templates/search.html")
			tmpl.Execute(w, result)
			if err != nil {
				log.Println(err)
			}
		}
	}

	log.Println("Result DATA:")
	for i, val := range result.Data {
		log.Println("	", i, val.UserName, val.ObjectName, val.ObjectDescription, val.CardId)
	}
}

func sphinxPatentSearch(q string, f PatentFilters) ([]int, int) {
	var ids []int
	shard := "patent"

	sc := sphinx.NewClient().SetServer("sphinx", 0).SetConnectTimeout(1000).SetLimits(0, 500, 500, 0)

	log.Println("Connected with Sphinx! Shard:", shard)
	sc.ResetFilters()

	log.Println("The query: <", q, ">")
	res, err := sc.Query(q, "patent", "patent")

	if err != nil {
		log.Println("Query error:", err)
	}

	if err = sc.Error(); err != nil {
		log.Fatal(err)
	}
	if res == nil {
		log.Println("Panic! res is nil! Len of filter:\n The query:", q, " and shard is ", shard, "\n and the filters are:", f)
		return ids, 0
	} else {
		ids = make([]int, len(res.Matches))
		log.Println("Post query: ", len(res.Matches), "/", res.Total)
		for i, match := range res.Matches {
			ids[i] = int(match.DocId)
		}
	}

	return ids, res.Total
}

func PatentSearch(db *sql.DB, query string, f PatentFilters) PatentResult {
	q := query
	var ans PatentResult

	log.Println("Let's do real index search now")
	ids, _ := sphinxPatentSearch(q, f)
	ans.Total = len(ids)
	log.Println(ids)

	if f.filterPage == -1 {
		f.filterPage = 0
		f.perPage = MaxDocs
	}

	data := new([]Patent)
	if len(ids) > 0 && f.filterPage >= 0 {
		get_page := `
			SELECT u.username, oi.name, oi.description, oi.card_id
			FROM users u
				JOIN object_info oi ON oi.autor_id = u.row
					AND oi.row IN (`

		for i, val := range ids[f.perPage*f.filterPage:] {
			if i == f.perPage {
				break;
			}

			if i == 0 {
				get_page += strconv.Itoa(val)
			} else {
				get_page += ", " + strconv.Itoa(val)
			}
		}
		get_page += ") ORDER BY oi.row DESC;"

		rows, err := db.Query(get_page)
		if err != nil {
			log.Fatal(err)
		}
		defer rows.Close()
		j := 0

		for rows.Next() {
			var row Patent
			err := rows.Scan(
				&row.UserName, &row.ObjectName, &row.ObjectDescription, &row.CardId,
			)
			row.Query = query
			*data = append(*data, row)
			if err != nil {
				log.Fatal(err)
			}
			log.Println("For Next Patent:", j, row.UserName, row.ObjectName, row.ObjectDescription, row.CardId)
			j++
		}
		log.Println("PerPages_______JJJ:", j)
		err = rows.Err()
		if err != nil {
			log.Fatal(err)
		}
	}
	ans.Data = concatPatent(ans.Data, *data)

	for i, val := range ans.Data {
		log.Println(i, val)
	}

	ans.Count = len(ans.Data)
	ans.Query = query
	ans.FilterPage = f.filterPage + 1
	ans.setMeta(query, f)
	log.Println(ans.Count, "/", ans.Total)
	//ans.Total = ans.Count

	return ans
}

func concatPatent(old1, old2 []Patent) []Patent {
	newslice := make([]Patent, len(old1)+len(old2))
	copy(newslice, old1)
	copy(newslice[len(old1):], old2)
	return newslice
}
