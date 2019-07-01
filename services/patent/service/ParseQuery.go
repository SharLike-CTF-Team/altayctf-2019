package main

import (
	"log"
	"regexp"
	"strings"
)

func dfWrap(s string) string {
	return "*" + s + "*"
}

func phWrap(s string) string {
	return "(" + s + ")"
}

func rmSpecialChars(s string) string {
	specialChars, err := regexp.Compile("[+*()\\'\"«»]+")
	if err != nil {
		log.Println("ParseQuery Error: ", err, " Query:", s)
	}
	s = specialChars.ReplaceAllString(s, "")
	return s
}

func parseQuery(q string) string {
	// log.Println("Parsing query:", q)
	// 0. - Trim spaces and commas
	q = strings.TrimSpace(q)
	// 1. OR:
	q1 := strings.Split(q, ",")
	// log.Println("q1:", q1)
	// 2. Trim spaces in q1 and split by spaces
	q2 := *new([][]string)
	for _, s := range q1 {
		if len(s) > 0 {
			s = strings.TrimSpace(s)
			q2 = append(q2, strings.Split(s, " "))
		}
	}
	// log.Println("q2:", q2)
	// 3. Wrapping the words
	q3 := *new([]string)
	for i, phrase := range q2 {
		for j, word := range phrase {
			word = rmSpecialChars(word)
			if string(word[0]) == "-" {
				if j != 0 {
					q2[i][j] = word + "*"
				} else {
					q2[i][j] = dfWrap(word[1:])
				}
			} else {
				q2[i][j] = dfWrap(word)
			}
		}
		q3 = append(q3, phWrap(strings.Join(q2[i], " ")))
	}
	// 4. Join all
	q4 := strings.Join(q3, "|")
	// log.Println("Final:", q4)
	return q4
}

/*func main() {
//	test1 := "  a b, c,g -d "
	test2 := "a b -c"
	parseQuery(test2)
}*/
