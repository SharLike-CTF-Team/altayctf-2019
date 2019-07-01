FROM python:3.7
RUN mkdir /app
COPY requirements.txt /app
RUN cd /app && pip install -r requirements.txt
RUN git clone https://github.com/manicmaniac/arc4.git && cd arc4 && python setup.py install
WORKDIR /app/kettle