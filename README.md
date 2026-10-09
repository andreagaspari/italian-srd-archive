# Italian SRD Archive

Archivio web in italiano dei contenuti dello **System Reference Document (SRD)**, compatibili con la v5.5 (2024).

L'applicazione legge i dati dai file JSON presenti in [`data/`](./data/) e li rende consultabili tramite un'interfaccia web con ricerca, filtri, paginazione ed esportazione.

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

Non è richiesto alcun database: i dati sono caricati direttamente dai file JSON in [`data/`](./data/).

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
