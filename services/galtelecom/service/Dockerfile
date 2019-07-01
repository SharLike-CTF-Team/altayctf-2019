FROM python:3.6
RUN mkdir /service
WORKDIR /service
COPY ./requirements.txt /service
RUN pip install -r requirements.txt
COPY . /service
CMD gunicorn manage:app -b 0.0.0.0:5000 -w 2
EXPOSE 5000
