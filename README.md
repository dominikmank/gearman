# Installation
Über Composer

    ```json
        {
            "require": {
                "dmank/gearman": "@stable"
            },
        }
    ```
# Benutzung
## Client
Beispiel Benutzung des Dispatchers

    ```php
        <?php
        require_once dirname(__FILE__).'/../vendor/autoload.php';

        // we can multiple servers due the collection
        $serverCollection = new \dmank\gearman\ServerCollection();
        // adding a local gearman connection
        $server = new \dmank\gearman\Server();
        $serverCollection->add($server);
        //tada , we can use the client!
        $client = new \dmank\gearman\Client($serverCollection);
    ```
    
### Asynchrone Abarbeitung
Methode "executeJobInBrackground", erwartet den Methodennamen der ausgeführt werden soll sowie den Workload.
Als Rückgabe bekommt man das zugewiesene Jobhandle zurück um den Status überprüfen zu können.

### Synchrone Abarbeitung
Methode "executeJob", erwartet Methodenname sowie Workload.
Als Rückgabe bekommt man den Joboutput zurück. Hier kann dementsprechend kein Status abgefragt werden.
### Jobstatus holen
Methode "getJobStatus", erwartet das JobHandle (string) als Übergabe Parameter.
Gibt ein Objekt "JobStatus" zurück welches die Methoden "isCompleted, isRunning, isKnown" anbietet.
Completed gilt der Job wenn er nicht mehr "isKnown" & "isRunning" ist. (Information von Gearman)

## Worker
Der Worker selber läuft solange wie er kann, sollte diesem kein Verhalten über das Eventing mitgeteilt werden.
Hier bietet sich zum Beispiel ein EventHandler an, der vor/nach jeder Funktionsausführung prüft wieviel RAM verbraucht wurde,
und im Zweifel den Worker beendet.
Ein weiteres Beispiel wäre eine maximale Zeit für den Worker einzustellen. Hier kann man zum Beispiel, wenn der Job zu lange im IDLE
Modus ist, diesen beenden.
Beispiel Worker

    ```php
    <?php
    require_once dirname(__FILE__).'/../vendor/autoload.php';
    
    // we can multiple servers due the collection
    $serverCollection = new \dmank\gearman\ServerCollection();
    // adding a local gearman connection
    $server = new \dmank\gearman\Server();
    $serverCollection->add($server);
    
    //no must have, but nice to have!
    $log = new \Monolog\Logger('jobworker');
    $streamHandler = new \Monolog\Handler\StreamHandler('php://output');
    $streamHandler->setLevel(\Monolog\Logger::DEBUG);
    $log->pushHandler($streamHandler);
    
    $eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
    $eventDispatcher->addSubscriber(new \dmank\gearman\event\subscriber\Monolog($log));
    
    $jobCollection = new \dmank\gearman\JobCollection();
    
    $worker = new \dmank\gearman\Worker(
        $serverCollection,
        $jobCollection,
        $eventDispatcher
    );
    
    $worker->run();
    ```
    
### Events
Der Worker stellt über Eventing folgende Events bereit: (In dieser Reihenfolge)
    * WorkerEvent::EVENT_BEFORE_RUN
    * ConnectToServerEvent::CONNECT_TO_SERVER_EVENT
    * ConnectToServerEvent::CONNECTED_TO_SERVER_EVENT
    * RegisterFunctionEvent::EVENT_ON_BEFORE_REGISTER_FUNCTIONS
    * RegisterFunctionEvent::EVENT_ON_AFTER_REGISTER_FUNCTIONS

Die folgenden werden Abfragenbedingt ausgeführt:

Sollte Gearman ein IO_WAIT zurückliefern

* WorkerEvent::EVENT_ON_IO_WAIT

Bei NO_JOBS

* WorkerEvent::EVENT_ON_NO_JOBS

Bei ON_WORK

* WorkerEvent::EVENT_ON_WORK

Bei SUCCESS

* WorkerEvent::EVENT_AFTER_RUN

Jeder registrierten Funktion werden folgende Events hinzugefügt
Vor Ausführung

* FunctionEvent::FUNCTION_BEFORE_EXECUTE

Nach erfolgreicher Ausführung

* FunctionEvent::FUNCTION_AFTER_EXECUTE

Im Fehlerfall

* FunctionFailureEvent::FUNCTION_ON_FAILURE