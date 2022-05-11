<?php
    permission_check("account");
    echo "<h2>Full Credit Statement: </h2>";
    echo "<hr>";
    echo "<div id='container'>";
    $transactions = $db->query("fh_ledger", null, "userID = '" . $uid . "'", false,  ["timestamp" => "DESC"]);
	
	if($transactions){
		echo "<table><tr>";
		echo "<th>From/For</th>";
		echo "<th>Bal Change</th>";
		echo "<th>Balance</th>";
		echo "<th>Time and Date</th>";

		echo "</tr>";
		foreach($transactions as $transaction){
    		echo "<tr>";
    		$loaner = $db->getRow("fh_adverts", "id=" . $transaction['advertID']);
    		if ($loaner){
        		echo "<td>" . (($transaction['TransactionType']==0) ? "TopUp" : "<a href='index.php?page=item&view_item=" . $transaction['advertID'] . "'>" . $loaner['name'] . "</a>") . "</td>";
    		}else{
    		    echo "<td>" . (($transaction['TransactionType']==0) ? "TopUp" : "<a href='index.php?page=item&view_item=" . $transaction['advertID'] . "'>Unknown Advert</a>") . "</td>";
    		}
    		echo "<td>" . $transaction['changeCredit'] . "</td>";
    		echo "<td>" . $transaction['balance'] . "</td>";
    		echo "<td>" . date('H:i:s d/M/Y',$transaction['timestamp']) . "</td>";
    		
            echo "</tr>";

		}
        echo "</table>";
    echo "</div>";
	}
