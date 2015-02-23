
<?php
//////////////// Singleton DataBase Connection  //////////////////////
//////////////////////////////////////////////////////////////////////
class bddConnect {
    private static $_bdd;
    
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
	
    public static function getBdd() {
				
        if (is_null(self::$_bdd))
		{
			new bddConnect();		
		}
		
		return self::$_bdd;		
    }
	
	 private function __clone()
    {
    }
   
    private function __wakeup()
    {
    }	
}

?> 