<?php
while ($row = mysqli_fetch_assoc($query)) {
    // $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
    //             OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
    //             OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";

    // $query2 = mysqli_query($conn, $sql2);
    // $row2 = mysqli_fetch_assoc($query2);
    // (mysqli_num_rows($query2) > 0) ? $result = $row2['msg'] : $result ="No message available";
    // (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
    // if(isset($row2['outgoing_msg_id'])){
    //     ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "You: " : $you = "";
    // }else{
    //     $you = "";
    // }
    // ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";
    // ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

    // $output .= '<a href="/chat?user_id=' . htmlspecialchars($row['unique_id']) . '">
    //                 <div class="content">
    //                     <img src="php/images/' . htmlspecialchars($row['img']) . '" alt="">
    //                     <div class="details">
    //                         <span>' . htmlspecialchars($row['fname'] . " " . $row['lname']) . '</span>
    //                         <p>' . $you . $msg . '</p>
    //                     </div>
    //                 </div>
    //                 <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
    //             </a>';
        $sql = "
        SELECT u.*
        FROM users u
        WHERE u.unique_id != {$outgoing_id}
        AND EXISTS (
            SELECT 1 FROM messages 
            WHERE 
                (incoming_msg_id = u.unique_id AND outgoing_msg_id = {$outgoing_id})
                OR 
                (outgoing_msg_id = u.unique_id AND incoming_msg_id = {$outgoing_id})
        )
        ";
        $query = mysqli_query($conn, $sql);

        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $sql2 = "
            SELECT * FROM messages 
            WHERE 
                ((incoming_msg_id = {$row['unique_id']} AND outgoing_msg_id = {$outgoing_id}) 
                OR 
                (outgoing_msg_id = {$row['unique_id']} AND incoming_msg_id = {$outgoing_id}))
            ORDER BY msg_id DESC LIMIT 1";

                $query2 = mysqli_query($conn, $sql2);
                $row2 = mysqli_fetch_assoc($query2);

                if (mysqli_num_rows($query2) > 0) {
                    $result = $row2['msg'];
                } else {
                    $result = "No message available";
                }

                // Shorten long messages
                $msg = (strlen($result) > 28) ? substr($result, 0, 28) . '...' : $result;

                // Identify sender
                if (isset($row2['outgoing_msg_id'])) {
                    $you = ($outgoing_id == $row2['outgoing_msg_id']) ? "You: " : "";
                } else {
                    $you = "";
                }

                // Online/offline status
                $offline = ($row['status'] == "Offline now") ? "offline" : "";
                $hid_me = ($outgoing_id == $row['unique_id']) ? "hide" : "";

                $output .= '
            <a href="/chat?user_id=' . htmlspecialchars($row['unique_id']) . '">
                <div class="content">
                    <img src="php/images/' . htmlspecialchars($row['img']) . '" alt="">
                    <div class="details">
                        <span>' . htmlspecialchars($row['fname'] . " " . $row['lname']) . '</span>
                        <p>' . $you . htmlspecialchars($msg) . '</p>
                    </div>
                </div>
                <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
            </a>';
            }
        } else {
            $output .= "<p class='text'>No conversations found.</p>";
        }

        echo $output;
}
