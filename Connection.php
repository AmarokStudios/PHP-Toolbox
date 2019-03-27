<?php
	// Set the timezone of your server. Used for the GetTime() method.
	Date_Default_TimeZone_Set("America/Chicago");
	// Save request data to variables in case they are needed.
	$RequestMethod = $_SERVER['REQUEST_METHOD'];
	$RequestTime = Date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
	$RequestUserAgent = $_SERVER['HTTP_USER_AGENT'];
	$RequestRemoteAddress = $_SERVER['REMOTE_ADDR'];
	
	Class Connection {
		Var $Link;
		Var $Type;
		Var $Result;
		Var $Uses;
		Var $LastQuery;
		Var $IsError;
		Var $ErrorMessage;
		// Creates and opens a database connection.
		Function __construct($cType = 1) {
			$this->Type = $cType;
			$this->OpenStream();
			$this->Uses = 0;
		}
		Function OpenStream() {
			// Switch/Case to handle connection types to allow multipe database connections.
			// To add more support, simply add another case and set the values.
			Switch ($this->Type) {
				Case 1:
					$Server = "localhost";
					$Username = "";
					$Password = "";
					$DB = "";
					Break;
				Default:
					Exit();
					Break;
			}
			$this->Link = MySQLi_Connect($Server, $Username, $Password, $DB);
			$this->Uses++;
			If (MySQLi_Connect_ErrNo()) {
				$this->IsError = TRUE;
				$this->ErrorMessage = "MySQLi Connection was not established: " . MySQLi_Connect_Error();
				Exit();
			}
		}
		// Manually close the connection.
		Function CloseStream() { MySQLi_Close($this->Link); }
		// Update database connection type
		Function UpdateSteam($Type) {
			$this->Type = $Type;
			$this->CloseStream();
			$this->Uses = 0;
			$this->OpenStream();
		}
		// Executes a query.
		Function Execute($SQL, $CloseStream = FALSE) {
			$this->Result = MySQLi_Query($this->Link, $SQL);
			$this->Uses++;
			$this->LastQuery = $SQL;
			If ($CloseStream) {
				$this->CloseStream();
			}
			Return 	$this->Result;
		}
		// Quickly executes a query without an instance.
		Static Function QuickExecute($SQL, $Type = 1) {
			$Connect = new Connection($Type);
			$Result = $Connect->Execute($SQL);
			$Connect->CloseStream();
			Return $Result;
		}
		Static Function GetTime() {
			$Timestamp = new DateTime(Date("Y-m-d H:i:s"));
			Return $Timestamp->format("Y-m-d H:i:s");
		}
		Static Function StopExec($Code = 200, $Link = NULL, $Message = "") {
			HTTP_RESPONSE_CODE($Code);
			If ($Message != "") { Echo $Message; }
			If ($Link !== null) { $Link->CloseStream(); }
			Exit();
		}
	}
?>