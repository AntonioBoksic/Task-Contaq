---Avvertenze:

ricordardi di cambiare la configurazione nel file .env per quanto riguarda il database.

Migration, seeder e faker vengono forniti col progetto quindi sarebbe opportuno popolare il database in modo da poter analizzare il progetto nella sua interezza.

!! N.B.
Per quanto riguarda politiche CORS notare che nel file backend/config/cors.php gli url ai queli è consentito chiamare il backend sono i seguenti:
'allowed_origins' => ['http://localhost:5176', "http://localhost:5174"],
Bisognerà aggiungere altri url in caso si voglia far girare il frontend su un url diverso

Inoltre tutte le chiamate axios chiamano l url http://localhost:8001, sarebbe bene configurare il proprio backend in locale su quel url a meno che non si voglia cambiare l url a tutte le chiamate axios (avrei dovuto salvare l'url da chiamare in una variabile globale/file config per poter cambiare velocemente l'url a tutte le chiamate, me ne rendo conto solo adesso.)
!!

---Sul progetto:

Nel momento in cui si apre http://localhost:5174 si atterra in homepage, nella navbar ci saranno i link di login e registrazione.
Una volta loggati, nella navbar apparirà un menù a tendina dal quale è possibile scegliere tra home, tickets, logout.
Una volta entrati in tickets sarà possibili visualizzare tutti i messaggi relativi a quel ticket cliccando su "messaggi" nella riga del ticket.

Ultima cosa da notare, a seconda che si logghi come operatore o tecnico ci sono alcune piccole differenze:
1)nella lista dei messaggi solo i tecnici vedono il nome dell'operatore nel messaggio, l'operatore giustamente non vede il proprio nome.
2)nella fase di modifica del ticket i campi modificabili sono diversi (operatore: titolo e categoria, tecnico: stato)
