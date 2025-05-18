
# 🔧 ARMConverter – ARM Instruction Converter

> A simple web interface to convert ARM instructions to hexadecimal and vice versa.  
> Built for developers, reverse engineers, and low-level enthusiasts.

![Language](https://img.shields.io/badge/language-PHP-informational?style=flat-square)
![Interface](https://img.shields.io/badge/interface-TailwindCSS-blueviolet?style=flat-square)
![Status](https://img.shields.io/badge/status-In%20Development-yellow?style=flat-square)

---

## 🚀 Overview

**ARMConverter** is a PHP-based web tool that allows converting ARM architecture instructions (ARM32 or ARM64) into hexadecimal and back. It features a clean, modern interface using **TailwindCSS** and **SweetAlert2** for a smooth user experience.

---

## 🖼️ Preview

[https://prnt.sc/yyIIdsyXW-Yl](https://prnt.sc/yyIIdsyXW-Yl)

---

## 📁 Project Structure

```
ARMConverter/
├── index.php         → Main web interface
├── encoder.php       → AJAX processing script
```

---

## ⚙️ Features

- 🔄 Architecture selection (ARM32 / ARM64)
- 📥 Instruction to hex conversion
- ⚡ Responsive interface with **TailwindCSS**
- 💬 Modern alerts via **SweetAlert2**
- ✅ No page reload (AJAX-based)

---

## 🛠️ Technologies Used

- PHP (>= 7.4)
- HTML5 / JavaScript
- TailwindCSS CDN
- SweetAlert2 CDN
- XMLHttpRequest / Fetch API

---

## 📦 Installation

1. Clone or download the project:
   ```bash
   git clone https://github.com/Francois284Modz/ARMConverter.git
   ```
2. Place it in your `htdocs` or `www` folder (if using XAMPP/Laragon):
   ```bash
   mv ARMConverter /path/to/htdocs/
   ```
3. Start your local server:
   ```bash
   http://localhost/ARMConverter/
   ```

---

## 💡 How to Use

1. Select `ARM32` or `ARM64`
2. Type an instruction (e.g., `mov r0, #1`)
3. Click "Convert"
4. See the resulting hex code or an error message

---

## 🧭 Roadmap

- 🔁 Reverse conversion: Hex to instruction
- 📤 Export results to `.txt`
- 📄 Local conversion history
- 🌐 Online public version

---


---

## 📃 License

This project is licensed under the MIT License.  
Feel free to use and adapt it, and give credit if used publicly. 😉

