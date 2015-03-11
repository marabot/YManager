
<?php
//////////////// Singleton DataBase Connection  //////////////////////
//////////////////////////////////////////////////////////////////////
class dbConnect {
    private static $_db;
    
	private function __construct(){
			try
			{
            self::$_bdd = new PDO('mysql:host=yourhost;dbname=yourDB;charset=utf8', 'user', 'password');
			}
			catch (Exception $e)
			{
			    die('Erreur : ' . $e->getMessage());
			}	
	}
	
    public static function getDb() {
				
        if (is_null(self::$_db))
		{
			new dbConnect();
		}
		
		return self::$_db;
    }
	
	 private function __clone()
    {
    }
   
    private function __wakeup()
    {
    }	
}
?> 