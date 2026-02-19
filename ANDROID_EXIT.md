# Tombol Keluar di Aplikasi Android (Web → App)

Aplikasi web ini sudah mendukung tombol **Keluar** di halaman login. Agar tombol itu benar-benar menutup aplikasi Android (bukan hanya menampilkan pesan), project Android Anda harus menangani salah satu cara berikut.

---

## Opsi 1: Tangkap URL Kustom (Paling umum untuk WebView)

Halaman login akan membuka URL: **`absensi://exit`** saat pengguna menekan Keluar.

### Di project Android (Kotlin)

Pada `WebViewClient` yang dipakai untuk WebView Anda, override `shouldOverrideUrlLoading`:

```kotlin
webView.webViewClient = object : WebViewClient() {
    override fun shouldOverrideUrlLoading(
        view: WebView?,
        request: WebResourceRequest?
    ): Boolean {
        val url = request?.url?.toString() ?: return false
        if (url == "absensi://exit") {
            finish() // tutup Activity
            return true
        }
        return false
    }
}
```

### Di project Android (Java)

```java
webView.setWebViewClient(new WebViewClient() {
    @Override
    public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
        String url = request.getUrl().toString();
        if ("absensi://exit".equals(url)) {
            finish();
            return true;
        }
        return false;
    }
});
```

---

## Opsi 2: JavaScript Interface (WebView)

Jika Anda bisa menambah JavaScript interface ke WebView, halaman login akan memanggil **`Android.closeApp()`** jika tersedia.

### Kotlin

```kotlin
webView.addJavascriptInterface(object {
    @JavascriptInterface
    fun closeApp() {
        runOnUiThread { finish() }
    }
}, "Android")
```

### Java

```java
webView.addJavascriptInterface(new Object() {
    @JavascriptInterface
    public void closeApp() {
        runOnUiThread(() -> finish());
    }
}, "Android");
```

---

## Jika Aplikasi Hanya “Di-generate” (Tanpa akses ke kode Android)

Banyak generator (Web to APK, PWA to APK) hanya menghasilkan APK tanpa memberi Anda kode sumber. Dalam kasus itu:

- Tombol Keluar akan menampilkan pesan: *"Untuk keluar: gunakan tombol kembali atau tutup aplikasi dari recent apps."*
- Itu adalah batasan platform; penutupan paksa harus dilakukan dari sisi native Android.

Jika generator Anda menyediakan **download source code** atau **custom URL scheme**, gunakan Opsi 1 di project tersebut.

---

## Mengganti Skema URL

Di file `resources/views/login/login.blade.php`, variabel JavaScript:

```javascript
var EXIT_APP_SCHEME = 'absensi://exit';
```

Anda bisa mengubah `absensi://exit` menjadi skema lain (misalnya `myapp://close`) asalkan sama dengan yang Anda tangkap di kode Android.
