from fastapi import FastAPI
import requests
import os
from dotenv import load_dotenv

load_dotenv()

app = FastAPI()

@app.post("/summarize")
def summarize(payload: dict):
    text = payload.get("text")

    response = requests.post(
        os.getenv("AI_API_URL"),
        headers={
            "Authorization": f"Bearer {os.getenv('GITHUB_TOKEN')}",
            "Content-Type": "application/json"
        },
        json={
            "model": "microsoft/Phi-4",
            "messages": [
                {
                    "role": "system",
                    "content": "Summarize the given text in simple bullet points"
                },
                {
                    "role": "user",
                    "content": text
                }
            ],
            "temperature": 0.8,
            "top_p": 0.1,
            "max_tokens": 2048
        }
    )

    return {
        "result": response.json()["choices"][0]["message"]["content"]
    }
