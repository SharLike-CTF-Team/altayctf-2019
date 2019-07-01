FROM golang:1.12-alpine3.9

RUN mkdir -p /go/src/patent_service
WORKDIR /go/src/patent_service

COPY . /go/src/patent_service
RUN apk add --no-cache git
RUN sh requirements.txt
ENV GOPATH=$GOPATH:/go/src/patent_service
RUN echo $GOPATH
RUN go build -o service

ENV SERVICE_PORT=80
ENV PG_HOST=postgres PG_PORT=5432 PG_DB=postgres PG_USER=postgres PG_PASSWORD=postgres

EXPOSE 80
CMD /go/src/patent_service/service >> /dev/null 2>&1
