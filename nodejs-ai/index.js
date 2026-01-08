import express from "express";
import fetch from "node-fetch";
import "dotenv/config";

const app = express();
app.use(express.json());

app.post("/summarize", async (req, res) => {
  try {
    const response = await fetch(process.env.AI_API_URL, {
      method: "POST",
      headers: {
        "Authorization": `Bearer ${process.env.GITHUB_TOKEN}`,
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        model: "microsoft/Phi-4",
        messages: [
          {
            role: "system",
            content: "Summarize the given text in simple bullet points"
          },
          {
            role: "user",
            content: req.body.text
          }
        ],
        temperature: 0.8,
        top_p: 0.1,
        max_tokens: 2048
      })
    });

    const data = await response.json();
    res.json({ result: data.choices[0].message.content });

  } catch (error) {
    res.status(500).json({ error: "AI request failed" });
  }
});

app.listen(3000, () => {
  console.log("Node AI server running at http://localhost:3000");
});

