
<?php
//////////////// Singleton DataBase Connection  //////////////////////
//////////////////////////////////////////////////////////////////////
class dbConnect {
    private static $_db;
    
	private function __construct(){
			try
			{
            self::$_db = new PDO('mysql:host=127.0.0.1;dbname=ymanager;charset=utf8', 'root', '22hum001');
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