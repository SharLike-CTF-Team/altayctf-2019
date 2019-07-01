FROM manticoresearch/manticore

WORKDIR /etc/sphinxsearch
COPY sphinx.conf .
COPY start.sh .
COPY wait-for.sh .
RUN chmod +x *.sh
CMD ["./start.sh"]
