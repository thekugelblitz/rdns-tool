# rdnsgen v1 - RDNS Record Automation Tool

A simple, secure, browser-based PHP tool to convert bulk RDNS entries into provider-compatible formats. This tool is optimized for web hosting professionals and sysadmins who regularly manage reverse DNS setups.

## Features

- ✏️ Accepts input via text area or `.txt` file upload (max 2 KB).
- 🧠 Auto-detects formats like:
  - `hostname,IP`
  - `IP PTR hostname`
  - Raw combinations without delimiter
- 🧹 Cleans up and corrects formats automatically.
- 📄 Previews the processed RDNS entries.
- 📥 Generates downloadable `.txt` file with a dynamic name:  
  `rdns_<IP-subnet>_<YYYYMMDD_HHMMSS>.txt`
- ✅ Ensures hostnames end with a dot (`.`).
- 🔐 Validates file type (`text/plain`) and restricts file size.

## Input Formats Supported

You can paste or upload RDNS records in any of these formats:

`int3rnet.net,192.168.100.101 192.168.100.101 PTR int3rnet.net int3rnet.net192.168.100.101`

The tool intelligently parses and formats them into:

`101 PTR int3rnet.net.`


## Security Measures

- Accepts only `.txt` files with MIME type `text/plain`
- Upload size limited to **2 KB**
- No files are saved on the server
- Processed data is passed via POST and generated dynamically

## How To Use

1. Upload a `.txt` file with RDNS records **OR** paste your RDNS entries into the textarea.
2. Click **Process Records**.
3. Review the formatted output in the preview section.
4. Click **Download Processed File** to download the generated `.txt`.

## Files

- `rdns.php` — Main interface and processor.
- `download.php` — Handles the download with proper headers.

## Installation

1. Upload the files to your PHP-enabled web server.
2. Access `rdns.php` via your browser.
3. Done!

## Example Output

If you input:

`web01.example.com,203.0.113.45`

The generated file will contain:

`45 PTR web01.example.com.`

And the download filename will look like:

> rdns_203.0.113_20250330_231509.txt

---

## 🛡️ License

This project is licensed under the **GNU General Public License v3.0**. See the [LICENSE](LICENSE) file for details.

---

## Author & Credits

Maintained & Developed by **Dhruval Joshi** from **[HostingSpell](https://hostingspell.com)**  
GitHub Profile: [@thekugelblitz](https://github.com/thekugelblitz) | 
This was created by Dhruval Joshi to use at HostingSpell and optimized with the help of GPT4 later.

---

If you want to contribute, feel free to fork and submit a PR! 🚀

Pull requests and contributions are welcome!

🛠 Created with ❤️ by Dhruval for smart RDNS management.

---











