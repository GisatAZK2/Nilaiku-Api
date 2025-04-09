FROM python:3.11-slim

WORKDIR /app

COPY . .

RUN pip install --no-cache-dir -r requirements.txt

ENV PORT=8080
ENV FLASK_API_KEY=c43649ac42bc8e0259106ffd7cb9571cda6a03a1010d2c2c6415bab08dbf98e3

EXPOSE 8080

CMD ["sh", "-c", "exec gunicorn --bind 0.0.0.0:8080 app:app"]
