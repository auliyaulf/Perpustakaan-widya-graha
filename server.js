import express from "express";
import cors from "cors";
import Groq from "groq-sdk";

const app = express();
const port = 3000;

// Middleware
app.use(cors()); 
app.use(express.json());

// Konfigurasi Groq AI
const groq = new Groq({
    apiKey: "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" // Segera ganti/amankan setelah berhasil
});

// Endpoint Chat
app.post("/chat", async (req, res) => {
    try {
        const { message, context } = req.body;

        // Validasi input
        if (!message || message.trim() === "") {
            return res.json({ 
                choices: [{ message: { content: "Halo! Ada yang bisa Wiga bantu?" } }] 
            });
        }

        const completion = await groq.chat.completions.create({
            messages: [
                {
                    role: "system",
                    content: `Kamu adalah Wiga, asisten ramah di Perpustakaan Widya Graha. 
                              Gunakan data buku berikut sebagai referensi jawaban: ${context}`
                },
                {
                    role: "user",
                    content: message 
                },
            ],
            model: "llama-3.1-8b-instant", 
        });

        const balasan = completion.choices[0].message.content;

        // Mengembalikan format JSON yang dikenali oleh front-end (choices -> message -> content)
        res.json({
            choices: [
                {
                    message: {
                        content: balasan
                    }
                }
            ]
        });

    } catch (error) {
        console.error("AI Error:", error);
        res.status(500).json({ 
            choices: [{ message: { content: "Maaf, Wiga sedang mengalami gangguan koneksi ke otak AI." } }] 
        });
    }
});

app.listen(port, () => {
    console.log("========================================");
    console.log(`Server Wiga Assistant Ready!`);
    console.log(`Berjalan di http://localhost:${port}`);
    console.log("========================================");
});