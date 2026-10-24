# Italian SRD Archive

Archivio web in italiano dei contenuti dello **System Reference Document (SRD)**, compatibili con la v5.5 (2024).

L'applicazione legge i dati dai file JSON presenti in [`data/`](./data/) e li rende consultabili tramite un'interfaccia web con ricerca, filtri, paginazione ed esportazione.

I preferiti sono salvati localmente nel browser tramite `localStorage`. Vengono memorizzati i riferimenti canonici nel formato `dataset:id`, non la copia completa dell'elemento; questo mantiene i preferiti compatibili con gli aggiornamenti dei dataset e non richiede un database.

## Contenuti

L'archivio mette a disposizione:

- incantesimi, filtrabili per livello, scuola e classe;
- mostri, filtrabili per tipo, taglia, grado di sfida e allineamento;
- oggetti suddivisi in:
  - armi;
  - armature;
  - munizioni;
  - strumenti;
  - equipaggiamento d'avventura;
- ricerca globale nell'archivio;
- glossario delle regole con introduzione, abbreviazioni, categorie e filtri;
- esportazione dei risultati e dei singoli elementi.
- tema chiaro/scuro con preferenza salvata nel browser.

## Tema

Il tema può essere cambiato dal pulsante con l'icona della luna o del sole
nell'intestazione. La scelta viene salvata localmente nel browser con
`localStorage` e viene mantenuta tra le visite. Alla prima apertura,
l'applicazione segue la preferenza del sistema operativo.

## Requisiti

- PHP 8.0 o superiore;
- web server con supporto PHP;
- estensione PHP `mbstring`;
- Apache con `mod_rewrite` per le route definite in [`.htaccess`](./.htaccess).

MySQL 8.0 o superiore e `pdo_mysql` sono necessari solo per la modalità
`MYSQL` e per l'importazione del catalogo.

## Installazione

L'applicazione supporta due modalità indipendenti:

- `JSON` (predefinita), senza database;
- `MYSQL`, con database e importazione dei JSON.

### 1. Preparare i file da pubblicare

Copiare sul server i file necessari all'esecuzione:

```text
index.php
api-search.php
export.php
.htaccess
assets/
data/
includes/
templates/
```

Creare `.env` a partire da `.env.example` solo se serve configurare la
sorgente dati:

```bash
cp .env.example .env
```

Il file `.env` contiene dati sensibili: non inserirlo nel repository e non
renderlo scaricabile via HTTP. Il file [`.htaccess`](./.htaccess) incluso nel
progetto ne blocca l'accesso quando viene usato Apache.

### 2. Scegliere la sorgente dati

Senza `.env`, oppure con:

```env
DATA_SOURCE=JSON
```

l'applicazione legge i file JSON dalla directory [`data/`](./data/) e non
richiede alcun database.

Per usare MySQL:

```env
DATA_SOURCE=MYSQL
DB_DSN=mysql:host=127.0.0.1;port=3306;dbname=italian_srd_archive;charset=utf8mb4
DB_USER=nome_utente
DB_PASSWORD=password
```

I valori ammessi per `DATA_SOURCE` sono solo `JSON` e `MYSQL`. Se la variabile
manca, il valore predefinito è `JSON`.

### 3. Installare la modalità MYSQL

Questa procedura serve solo se `DATA_SOURCE=MYSQL`.

Creare il database con lo schema per una nuova installazione:

```bash
mysql -u root -p < database/schema.sql
```

Lo schema crea il database `italian_srd_archive`. Dopo aver configurato `.env`,
importare tutti i dataset JSON:

```bash
php bin/import-catalog.php
```

L'importatore è idempotente: può essere eseguito nuovamente dopo un
aggiornamento dei JSON senza ricreare il database né duplicare gli elementi.
Conserva il documento completo in `payload`, il testo per la ricerca in
`search_text` e usa `dataset` + `item_id` come chiave stabile.

### 4. Importazione senza accesso CLI

L'importazione da CLI è il metodo consigliato. Se l'hosting non offre SSH o un
terminale, è possibile usare temporaneamente `import-catalog-web.php`:

1. caricare `import-catalog-web.php` nella directory pubblica;
2. aggiungere a `.env` un token casuale:

   ```env
   IMPORT_TOKEN=token-casuale-e-lungo
   ```

3. inviare una richiesta `POST` con l'header `X-Import-Token`:

   ```bash
   curl -X POST \
     -H "X-Import-Token: token-casuale-e-lungo" \
     https://example.com/italian-srd-archive/import-catalog-web.php
   ```

L'endpoint rifiuta le richieste `GET` e richiede un token configurato. Dopo
aver verificato l'importazione, eliminarlo immediatamente insieme a
`IMPORT_TOKEN` dal `.env`. Non lasciare questo endpoint online.

### 5. Pulizia dopo l'installazione

Questi elementi non sono necessari al funzionamento ordinario e possono
restare solo nel repository o nell'ambiente locale:

```text
database/              # schema per la creazione iniziale
tests/                 # test locali
import-catalog-web.php # endpoint temporaneo, da eliminare sempre
bin/migrate.php        # vecchio script, se presente
.user.ini              # eventuale file temporaneo per il debug
```

`bin/import-catalog.php` può essere rimosso se non si dispone di accesso CLI o
non si prevedono futuri reimport. È preferibile mantenerlo fuori dalla
directory pubblica oppure conservarlo sul server solo per esecuzioni CLI.

Con `DATA_SOURCE=JSON` è possibile rimuovere anche `.env` e tutti i componenti
MySQL. Con `DATA_SOURCE=MYSQL`, invece, il database deve rimanere disponibile;
la directory `database/` non viene letta durante il normale funzionamento.

### Aggiornamento dei dati

I file JSON in [`data/`](./data/) restano la sorgente ufficiale. Per aggiornare
un'installazione MYSQL:

1. sostituire i JSON nella directory `data/`;
2. eseguire nuovamente `php bin/import-catalog.php` da CLI, oppure usare
   temporaneamente l'importazione web descritta sopra;
3. rimuovere l'endpoint web e il relativo token.

Non è necessario ricreare il database.

## Glossario delle regole

Il Glossario delle regole è pubblicato come dataset JSON in
[`data/rules.json`](./data/rules.json), estratto dall'SRD italiano 5.2.1
[PDF ufficiale](https://media.dndbeyond.com/compendium-images/srd/5.2/IT_SRD_CC_v5.2.1.pdf).
Il dataset contiene:

- l'introduzione completa con le convenzioni del glossario;
- le abbreviazioni con il relativo significato;
- le 154 voci del glossario;
- le categorie `azione`, `area di effetto`, `atteggiamento`, `condizione` e
  `pericolo`.

Nei nomi delle voci i descrittori tra parentesi quadre, per esempio
`Accecato [condizione]`, vengono rimossi dal titolo e conservati nel campo
`category`. Questo permette di filtrare le regole senza perdere
l'informazione originale. Le abbreviazioni presenti nel testo delle regole
vengono mostrate come tooltip nella pagina [`/regole`](./templates/rules/index.php).

In modalità `DATA_SOURCE=MYSQL`, il glossario viene importato nelle tabelle
`rule_documents` e `rule_items`, incluse introduzione, abbreviazioni, categorie
e voci. In modalità `DATA_SOURCE=JSON`, `rules.json` resta la sorgente
utilizzata dall'applicazione.

## Avvio locale

Dalla directory del progetto è possibile avviare il server web integrato di PHP:

```bash
php -S localhost:8000
```

Aprire quindi [http://localhost:8000](http://localhost:8000) nel browser.

In alternativa, il progetto può essere pubblicato in un ambiente Apache/PHP. Il file [`.htaccess`](./.htaccess) contiene le regole necessarie per il routing dell'applicazione.

## Struttura del progetto

```text
.
├── api-search.php       # Endpoint JSON per la ricerca globale
├── assets/              # CSS, JavaScript e risorse grafiche
├── data/                # Dataset JSON dell'archivio
│   └── rules.json       # Glossario delle regole e metadati introduttivi
├── export.php           # Endpoint per l'esportazione dei risultati
├── includes/            # Bootstrap, caricamento dati e logica applicativa
├── templates/            # Layout e viste HTML
├── tests/               # Test funzionali del catalogo
└── index.php            # Entry point dell'applicazione
```

## Preferiti

Ogni elemento del catalogo ha un ID stabile all'interno del proprio dataset. La chiave usata dai preferiti combina dataset e ID, per esempio `spells:aculeo-mentale` o `armor:scudo`, così anche ID uguali in categorie diverse non entrano in conflitto.

La lista viene salvata nella chiave `isa:favorites:v1` di `localStorage`. È quindi specifica del browser e del dispositivo: non viene sincronizzata e può essere persa cancellando i dati del sito. La pagina dei preferiti segnala gli elementi che non sono più presenti nei JSON correnti.

La struttura versionata e l'uso di riferimenti invece dei dati completi permettono di sostituire in futuro l'implementazione locale con un archivio associato a un account e a più liste, senza cambiare il formato con cui gli elementi del catalogo vengono identificati.

## Test

I test del catalogo possono essere eseguiti con:

```bash
php tests/catalog_test.php
```

## Licenza e attribuzione

Quest'opera include materiale tratto dalla versione italiana del **System Reference Document (SRD)** di **Wizards of the Coast LLC**, disponibile all'indirizzo:

<https://www.dndbeyond.com/srd>

Il SRD è concesso in licenza ai sensi della **licenza di attribuzione 4.0 Internazionale di Creative Commons (CC BY 4.0)**:

<https://creativecommons.org/licenses/by/4.0/legalcode>

Informazioni sulla licenza:

- Licenza: **CC BY 4.0**
- Testo sintetico della licenza: <https://creativecommons.org/licenses/by/4.0/>
- Versione SRD: **5.2.1**
- Versione compatibile dell'archivio: **v5.5 (2024)**
- Autore del progetto: **Andrea Gaspari**
- Sito dell'autore: <https://andreagaspari.dev>
