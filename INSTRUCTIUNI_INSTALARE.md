# Instrucțiuni de Instalare - WooCommerce Victoriabank Payments

## Prezentare Generală

Acest plugin permite integrarea sistemului de plăți Victoriabank în magazinele WooCommerce, oferind suport pentru 3 metode de plată:

- **Card Visa/Mastercard** - acceptă toate cardurile Visa sau Mastercard
- **Star Card Rate** - acceptă doar cardurile Star emise de BC Victoriabank  
- **Star Points** - plată cu puncte Star de pe cardurile Star emise de BC Victoriabank

## Cerințe de Sistem

- ✅ WordPress 6.0 sau mai nou
- ✅ WooCommerce plugin instalat și activat
- ✅ PHP 7.2 sau mai nou
- ✅ Cont de comerciant activ la Victoriabank
- ✅ Windows pentru generarea cheilor RSA

## Pas 1: Pregătirea Fișierelor

### Descărcarea Plugin-ului

În acest repozitoriu GitHub găsiți următoarele fișiere esențiale:

📦 **`woocommerce-victoriabank-payments.zip`** - Plugin-ul WordPress complet pregătit pentru instalare

🔧 **`RSAKeyGenerator.exe`** - Utilita pentru generarea cheilor RSA publice și private

## Pas 2: Instalarea Plugin-ului în WordPress

### Metoda 1: Prin Interfața WordPress (Recomandat)

1. **Conectați-vă** la panoul de administrare WordPress
2. **Navigați** la `Plugins > Add New Plugin`
3. **Faceți clic** pe butonul `Upload Plugin`
4. **Selectați** fișierul `woocommerce-victoriabank-payments.zip` de pe computerul dumneavoastră
5. **Faceți clic** pe `Install Now`
6. **Așteptați** finalizarea instalării
7. **Faceți clic** pe `Activate Plugin`

### Metoda 2: Instalare Manuală prin FTP

1. **Extrageți** conținutul fișierului `woocommerce-victoriabank-payments.zip`
2. **Conectați-vă** la serverul web prin FTP
3. **Încărcați** folderul extras în directorul `/wp-content/plugins/`
4. **Navigați** la `Plugins > Installed Plugins` în WordPress
5. **Găsiți** "Victoriabank payment" și faceți clic pe `Activate`

## Pas 3: Generarea Cheilor RSA

### ⚠️ Acest pas este OBLIGATORIU pentru securitatea tranzacțiilor

Pentru ca plugin-ul să funcționeze corect și sigur, aveți nevoie de chei RSA pentru criptarea comunicației cu sistemul bancar.

### Utilizarea RSAKeyGenerator.exe

1. **Descărcați** `RSAKeyGenerator.exe` din acest repozitoriu
2. **Rulați aplicația** pe un computer Windows (necesită drepturi de administrator)
3. **Urmăriți instrucțiunile** din aplicație
4. **Aplicația va genera:**
   - 🔐 **Cheia privată** (private key) - PĂSTRAȚI-O ÎN SIGURANȚĂ!
   - 🔓 **Cheia publică** (public key) - pentru transmiterea către Victoriabank

### 🚨 IMPORTANT - Securitatea Cheilor

- ❌ **NU distribuiți niciodată cheia privată**
- ✅ **Păstrați cheia privată într-un loc sigur**
- ✅ **Faceți backup-uri ale cheilor**
- ✅ **Cheia publică va fi trimisă către Victoriabank**

## Pas 4: Configurarea Inițială

### Accesarea Setărilor Plugin-ului

1. **Navigați** la `Settings > VB payments settings` în WordPress
2. **Veți vedea** mai multe secțiuni de configurat:

### Merchant Data (Date Comerciant)

Completați cu informațiile primite de la Victoriabank:

- 🏪 **Merchant ID** - identificatorul unic al comerciantului
- 🖥️ **Terminal ID** - identificatorul terminalului
- 📧 **Email adrese** pentru notificări
- 🏢 **Alte date specifice** furnizate de bancă

### Connection Settings (Setări de Conectare)

- 🌐 **URL Gateway** - adresa sistemului bancar
- 🔧 **URL-uri callback** - pentru răspunsurile automate
- 🎛️ **Environment** - Test sau Production

### Payment Settings (Setări de Plată)

- 💳 **Transaction Type** - Authorization sau Charge
- 💰 **Valute acceptate** - MDL, EUR, USD
- ⚙️ **Configurări specifice** pentru fiecare metodă de plată

## Pas 5: Configurarea Metodelor de Plată WooCommerce

### Activarea Metodelor de Plată

1. **Navigați** la `WooCommerce > Settings > Payments`
2. **Veți vedea** următoarele opțiuni noi:
   - 💳 **Victoriabank Visa/Mastercard**
   - ⭐ **Victoriabank Star Card Rate**
   - 🎯 **Victoriabank Star Points**

### Configurarea Individuală

Pentru fiecare metodă de plată:

1. **Faceți clic** pe `Manage` sau `Set up`
2. **Activați** metoda de plată (`Enable this payment method`)
3. **Configurați** titlul și descrierea pentru clienți
4. **Setați** ordinea de afișare
5. **Salvați** modificările

## Pas 6: Configurarea Email-urilor

### Dezactivarea Șabloanelor WooCommerce Standard

Pentru a utiliza șabloanele personalizate ale plugin-ului:

1. **Navigați** la `WooCommerce > Settings > Emails`
2. **Dezactivați** următoarele șabloane:
   - ❌ **New order**
   - ❌ **Processing order** 
   - ❌ **Completed order**

3. **Plugin-ul** va folosi propriile șabloane optimizate pentru plățile cu cardul

## Pas 7: Testarea Sistemului

### Configurarea Mediului de Test

1. **Asigurați-vă** că aveți `Environment = Test` în setările de conectare
2. **Utilizați** URL-urile de test furnizate de Victoriabank
3. **Folosiți** datele de test pentru Merchant ID și Terminal ID

### Procesul de Testare

1. **Creați** o comandă de test în magazinul dumneavoastră
2. **Selectați** una dintre metodele de plată Victoriabank
3. **Verificați** redirecționarea către pagina de plată
4. **Testați** procesul complet de plată
5. **Verificați** primirea notificărilor și email-urilor

### Ce să Verificați

- ✅ Redirecționarea funcționează corect
- ✅ Pagina de plată se încarcă
- ✅ Tranzacția de test se procesează
- ✅ Comanda se actualizează în WooCommerce
- ✅ Email-urile se trimit corect

## Pas 8: Trecerea în Mediul de Producție

### Când să Faceți Tranziția

Doar după ce:
- ✅ Toate testele funcționează perfect
- ✅ Ați primit confirmarea de la Victoriabank
- ✅ Datele de producție au fost furnizate

### Actualizarea Setărilor

1. **Schimbați** `Environment` din `Test` în `Production`
2. **Actualizați** URL-urile cu cele de producție
3. **Înlocuiți** datele de test cu cele reale
4. **Verificați** din nou toate setările

## Depanare și Probleme Comune

### Plugin-ul Nu Apare După Instalare

**Cauze posibile:**
- WooCommerce nu este instalat sau activat
- Versiunea PHP este prea veche (< 7.2)
- Conflict cu alte plugin-uri

**Soluții:**
- Verificați că WooCommerce este activ
- Actualizați PHP la versiunea 7.2+
- Dezactivați temporar alte plugin-uri pentru testare

### Erori la Generarea Cheilor RSA

**Cauze posibile:**
- Aplicația nu rulează pe Windows
- Lipsesc drepturi de administrator
- Software antivirus blochează aplicația

**Soluții:**
- Utilizați un computer Windows
- Rulați ca administrator
- Adăugați excepție în antivirus

### Probleme de Conectare la Victoriabank

**Cauze posibile:**
- Date comerciant incorecte
- URL-uri greșite
- Chei RSA invalid configurate

**Soluții:**
- Verificați datele primite de la bancă
- Contactați suportul Victoriabank
- Regenerați cheile RSA dacă este necesar

### Tranzacțiile Nu Se Procesează

**Verificări:**
- Status-ul comenzii în WooCommerce
- Log-urile plugin-ului (`/logs/` folder)
- Setările de callback URL
- Configurarea corectă a metodelor de plată

## Suport și Asistență

### Pentru Probleme Tehnice

📞 **Suport Victoriabank:**
- Contactați departamentul de suport tehnic
- Furnizați detalii complete despre eroare
- Includeți log-urile din plugin

### Pentru Probleme de Configurare

📧 **Email Support:**
- Includeți capturi de ecran cu setările
- Descrieți pașii urmați
- Menționați versiunea WordPress și WooCommerce

### Resurse Utile

- 📖 Documentația tehnică Victoriabank
- 🔍 Log-urile din `/logs/` folder
- ⚙️ Setările de debug din WordPress

## Lista de Verificare Finală

Înainte de a considera instalarea completă:

- [ ] Plugin-ul este instalat și activat
- [ ] Cheile RSA sunt generate și configurate
- [ ] Datele comerciantului sunt completate
- [ ] Metodele de plată sunt activate în WooCommerce
- [ ] Email-urile sunt configurate corect
- [ ] Testarea în mediul de test este realizată cu succes
- [ ] Tranziția la mediul de producție este finalizată
- [ ] Documentația și backup-urile sunt pregătite

---

**Versiunea documentației:** 1.1.0  
**Ultima actualizare:** 2025  
**Plugin version:** 1.1.0 