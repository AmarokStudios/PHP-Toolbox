<?php
	/* ============================================================================
		| Project			: PHP Toolbox
		| Class				: Connection
		| Author            : Timothy Bomer/Amarok Studios
		| Date created      : 09-20-2018
		| Description       : Easily manage database connections for MySQL.
	============================================================================ */

	Class Connection {																				// Starts the connection class.
		Var $Link;																					// Holds the SQL Connection.
		Var $Type;																					// Defines which connection to use. (This class supports multiple databases).
		Var $Res;																					// Holds the execution result. This allows you to re-use the result without re-running the query.
		Var $Uses;																					// Counts the number of times a connection is made to the database.
		Var $LastQuery;																				// Stores the last requested query to the database.
		Function __construct($cType = 1) {															// The constructor for the class sets the type to the first database if nothing is specified.
			$this->Type = $cType;																	// Sets the type. $this is used to allow multiple instances of the Connection class.
			$this->openConnection();																// Automatically opens the connection when an instance is created.
			$this->Uses = 0;																		// Sets the usage to 0.
		}																							// End of Constructor method.
		Function openConnection() {																	// Method for opening database connection.
			Switch($this->Type) {																	// Switch/Case to select database. To support more databases, simply add another case with the next integer value.
				Case 1:																				// Setup the first database.
					$Server = "localhost";															// Sets the server the database is located on. This is generally 'localhost' unless it's for a remote database.
					$Username = "";																	// Sets the username associated with the database.
					$Password = "";																	// Sets the user's password for the database.
					$dbName = "";																	// Sets the name of the database.
					Break;																			// Ends the case for the first database.
				Default:																			// Case to handle an invalid selection. (Example, only one database is set and the user selects number 2, which doesn't exist).
					Error_Log("Invalid connection type!");											// Prints error to error_log for debugging.
					Exit();																			// Exits the script.
			}																						// End of database selection switch/case.
			$this->Link = MySQLi_Connect($Server, $Username, $Password, $dbName);					// Initiates a connection and saves it to the Link variable.
			$this->Uses++;																			// Increments the Usages variable as a request is made to the database.
			If (MySQLi_Connect_ErrNo()) {															// Checks to see if there was an issue creating the connection.
				Error_Log("MySQLi Connection was not established: " . MySQLi_Connect_Error());		// If there was an error, print it to the error_log file.
				Exit();																				// Exits the script if an error occured.
			}																						// End of the connection error check.
		}																							// End of the open connection method.
		Function switchLink($Type) {																// This method switches between database connections. It closes the original, resets the usage, then opens the new one.
			$this->Type = $Type;																	// Sets the connection type of this instance to the new type.
			If ($this->Link !== NULL) {																// Checks to make sure there is an open connection.
				$this->closeConnection();															// Closes the open connection if it exists.
			}																						// End of the open connection check.
			$this->Uses = 0;																		// Resets the usage as it's a new connection.
			$this->openConnection();																// Opens the new connection (If it doesn't fail due to invalid type).
		}																							// End of the switch link method.
		Function closeConnection() {																// Method to close the connection.
			If ($this->Link !== NULL) {																// Checks to see if there is an open connection.
				MySQLi_Close($this->Link);															// Close the connection if it exists.
				$this->Link = NULL;																	// Nullifies the link.
			}																						// End of the connection check.
		}																							// End of the close connection method.
		Function execute($SQL) {																	// Executes a MySQL query.
			If ($this->Link !== NULL) {																// Checks to see if there is an open connection before executing.
				$this->Res = MySQLi_Query($this->Link, $SQL);										// Executes the query and saves the results.
				$this->Uses++;																		// Increments the usage as a request was made to the database.
				$this->LastQuery = $SQL;															// Saves the last query for debugging if needed.
				Return 	$this->Res;																	// Returns the result.
			} Else {																				// Handler for no open connection when executing.
				Error_Log("Could not execute query as there is not an open connection.");			// Print error to error_log for debugging.
				Exit();																				// Exit the script.
			}																						// End open connection check.
		}																							// End SQL execution method.
		Public Static Function closeExit($Link) {													// Static method to close the connection and forcefully exit the method. (Does not require the class to be intantiated)
			If ($Link !== NULL) {																	// Checks to see if the specified link is open.
				$Link->closeConnection();															// Closes the database connection.
			}																						// End of connection check.
			Exit();																					// Exits the script.
		}																							// End of close and exit method.
		Public Static Function quickSQL($SQL, $Type = 1) {											// Static method to quickly execute SQL. You do not need to open/close an instance. You may pass a type to select a database.
			$Connect = new Connection($Type);														// Opens a connection.
			$Result = $Connect->execute($SQL);														// Executes the requested SQL and saves the result.
			$Connect->closeConnection();															// Closes the connection.
			Return $Result;																			// Returns the result.
		}																							// End of the quick SQL execution method.
	}																								// End of the Connection class.
?>