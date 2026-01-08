using System.Net.Http.Headers;

var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

app.MapPost("/summarize", async (string text) =>
{
    var client = new HttpClient();
    client.DefaultRequestHeaders.Authorization =
        new AuthenticationHeaderValue(
            "Bearer",
            Environment.GetEnvironmentVariable("GITHUB_TOKEN")
        );

    var response = await client.PostAsJsonAsync(
        Environment.GetEnvironmentVariable("AI_API_URL"),
        new
        {
            model = "microsoft/Phi-4",
            messages = new[]
            {
                new { role = "system", content = "Summarize the given text in simple bullet points" },
                new { role = "user", content = text }
            },
            temperature = 0.8,
            top_p = 0.1,
            max_tokens = 2048
        }
    );

    return Results.Ok(await response.Content.ReadAsStringAsync());
});

app.Run();

