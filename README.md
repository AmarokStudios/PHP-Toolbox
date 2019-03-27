# PHP-Toolbox
Tools for PHP to make development a little easier!

### Connection Class Usage
```
// Create Instance using default values
$Connect = new Connection();
// Create instance with specific connection type
$Connect = new Connection($Type); // $Type is an integer matching the case in OpenStream();
```
```
// Manually close a database connection stream
$Connect = new Connection();
$Connect->CloseStream();
```
```
// Change from the current connection type to a new type.
$Connect = new Connection();
$Connect->UpdateStream($Type); // $Type is an integer matching the case in OpenStream();
```
```
// Execute an SQL query.
$Connect = new Connection();
$Connect->Execute($SQL);
// Execute query and close connection.
$Connect->Execute($SQL, true);
```
```
// Execute an SQL query without creating a connection instance. This is good for single queries.
Connection::QuickExecute($SQL);
// OR, if you want to specify a connection type
Connection::QuickExecute($SQL, $Type);
```
```
// Gets the current timestamp (make sure to set the timezone)
Connection::GetTime();
```
```
// Stops execution of the script (good for returning results)
Connection::StopExec();
// Stops execution of the script, and sets the HTTP response code.
Connection::StopExec($Code);
// Stops execution of the script, sets the response code, and closes the database connection stream
$Connect = new Connection();
Connection::StopExec(200, $Connect);
// Stops the execution, sets the response code, closes stream, and echos some text. NOTE: using this will ALWAYS set the response code to 200.
$Connect = new Connection();
Connection::StopExec(200, $Connect, "This is a test!");
```
