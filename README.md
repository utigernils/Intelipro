# Inteli Pro

**Inteli Pro** is a lightweight PHP-based tool that answers natural language questions about projects stored in a project database. It uses the DeepSeek API to interpret queries and respond with relevant information.

## 🔍 What It Does

Inteli Pro connects to your project database and allows users (for example on your portfolio) to ask questions like:

- "What programming langauge did Project XYZ use ?"
- "How many cameras does the XYZ have ?"

The questions are processed via the DeepSeek API, and the answers are generated based on your actual project data.

## 🚧 Built for Extensibility

The software includes a clean API scaffolding to make it easy for developers to:

- Add new endpoints
- Extend question handling
- Integrate with different databases or UIs

## 💻 How to Use

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/inteli-pro.git
   ```

2. **Configure the Environment**

   Copy this example to `config.ini` and update the settings:
   ```env
    [environment]
    base_path = intelipro/
    content_type = application/json
    mode = prod
    [database]
    db_host = localhost
    db_name = projects
    db_user = root
    db_pass = 
    [deepseek]
    deepseek_key = XXXXX
   ```

3. **Set Up the Project**

   - Ensure PHP 8.1+ and Composer are installed.

4. **Start the Built-in PHP Server**
   ```bash
   php -S localhost:8000 -t public
   ```

5. **Ask Questions via the API**

   Send a POST request to `/api` with a JSON payload:
   ```json
   {"query":"What programming language did the cubesolver use ?"}
   ```
   
## 🧩 Tech Stack

- PHP (vanilla with my "micro-framework")
- DeepSeek API
- MySQL DB

## 📝 License

MIT License

---

Created by Nils — Contributions welcome!
