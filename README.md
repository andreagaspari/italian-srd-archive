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
- esportazione dei risultati e dei singoli elementi.

## Requisiti

- PHP 8.0 o superiore;
- web server con supporto PHP;
- estensione PHP `mbstring`.
- Per l'importazione del catalogo: estensione PHP `pdo_mysql` e MySQL 8.0 o superiore.

Non è richiesto alcun database: i dati sono caricati direttamente dai file JSON in [`data/`](./data/).

## Preparazione del catalogo MySQL

Il catalogo continua a funzionare tramite JSON. Per installare la variante con database è disponibile una procedura unica:

```bash
mysql -u root -p < database/schema.sql
```

Lo schema crea il database `italian_srd_archive`. Dopo la creazione dello schema:

```bash
cp .env.example .env
# configurare DB_DSN, DB_USER e DB_PASSWORD in .env
php bin/import-catalog.php
```

`DB_PASSWORD` non deve essere inserita nel repository. L'importatore conserva il documento originale in `payload`, costruisce il testo indicizzato in `search_text` e usa la coppia `dataset` + `item_id` come chiave stabile. La chiave applicativa completa resta disponibile nel formato `dataset:item_id`. Ripetere l'importazione aggiorna gli elementi esistenti senza duplicarli.

La fonte dati dell'applicazione è controllata dalla variabile `DATA_SOURCE`:

```env
DATA_SOURCE=JSON
```

I valori ammessi sono:

- `JSON` (predefinito): legge i file in [`data/`](./data/);
- `MYSQL`: usa il database configurato tramite `DB_DSN`, `DB_USER` e `DB_PASSWORD`.

Lo stesso valore viene applicato anche alla ricerca globale dell’header.

Per usare il database:

```env
DATA_SOURCE=MYSQL
```

Se `DATA_SOURCE` contiene un valore diverso da `JSON` o `MYSQL`, l'applicazione interrompe l'avvio con un errore esplicito.

### Aggiornamento dei dati

I file JSON in [`data/`](./data/) restano la sorgente importabile. Dopo averli aggiornati, non è necessario ricreare il database: è sufficiente eseguire nuovamente:

```bash
php bin/import-catalog.php
```

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
