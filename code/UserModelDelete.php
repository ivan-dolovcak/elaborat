<?php
class UserModel
{
    function delete() : void
    {
        $query = "DELETE from `User`
            WHERE `ID` = ?";

        $DB = DB::getInstance();
        $DB->execStmt($query, "i", $this->ID);
    }
}
