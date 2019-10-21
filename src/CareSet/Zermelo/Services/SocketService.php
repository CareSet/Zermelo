<?php
/**
 * Created by PhpStorm.
 * User: kchapple
 * Date: 3/11/19
 * Time: 10:35 AM
 */

namespace CareSet\Zermelo\Services;

use CareSet\Zermelo\Models\Socket;
use CareSet\Zermelo\Models\SocketUser;
use CareSet\Zermelo\Models\Wrench;
use Illuminate\Support\Facades\Auth;

class SocketService
{
    protected $activeSockets = [];

    /**
     * @param $input
     *
     * Input comes in the form of an array withe the wrench ID as the index, and socket ID as value
     *
     */
    public function setSocketsFromApiInput( $input )
    {
        // Set the sockets for the current report, and save if we have a user
        $user = Auth::user();
        if ( $user !== null ) {
            $userId = $user->id;
            // First delete our old settings
            SocketUser::where('user_id', $userId)->delete();
            foreach ($input as $wrenchId => $socketId) {
                // Save the user's setting
                $socketUser = new SocketUser([
                    'user_id' => $userId,
                    'wrench_id' => $wrenchId,
                    'current_chosen_socket' => $socketId
                ]);
                $socketUser->save();
            }
        }

        // Now set the active sockets for the view (we do this even if there's no user for the current request)
        foreach ($input as $wrenchId => $socketId) {
            $socket = Socket::find( $socketId);
            $this->activeSockets[] = $socket;
        }

        return $this->activeSockets;
    }

   /*
	Get all of the sockets for a given wrenchString aand 
	return then as an associative array... 
   */
    public function fetchAllSocketsForWrenchKey( $key )
    {
        $wrench = Wrench::where( 'wrench_lookup_string', $key )->first();
        if ( $wrench !== null ) {

		//I know their is an Eloquent way to do this.. not work the bother...  
		$sockets = Socket::where('wrench_id', $wrench->id)->get();

		$socket_list_to_return = [];

		//lets build a simple flat list with the expected contents... 
		foreach($sockets as $this_socket){
			$socket_list_to_return[$this_socket->id] = [
						'socket_label' => $this_socket->socket_label,
						'socket_value' => $this_socket->socket_value,
						'is_default_socket' => $this_socket->is_default_socket,
						'wrench_id' =>  $wrench->id,
						'wrench_lookup_string' => $wrench->wrench_lookup_string,
						'wrench_label' => $wrench->wrench_label,
					];
		}

		return($socket_list_to_return);

        } else {
            throw new \Exception("Zermelo SocketWrench Error: No Wrench found for lookup string=`$key`");
        }

        return $foundSocket;
    }


    /**
     * @param $key
     * @return mixed|null
     * @throws \Exception
     *
     * Given a "key" or a wrench lookup string, get the "active" socket. This could be
     * the default socket, if none has been explicitly selected by the user, or the active
     * socket for this wrench as selected by the user via the data options user interface.
     */
    public function fetchSocketForWrenchKey( $key )
    {
        // Get the wrench model by lookup string from the config database
        $wrench = Wrench::where( 'wrench_lookup_string', $key )->first();
        if ( $wrench !== null ) {

            // Search the "active" sockets for this wrench's selected socket. If the user has
            // selected a socket for this wrench label via the user interface, it will be found
            $foundSocket = null;
            foreach ( $this->activeSockets as $activeSocket ) {
                if ( $wrench->id === $activeSocket->wrench_id ) {
                    $foundSocket = $activeSocket;
                    break;
                }
            }

            // If there's no active socket for this wrench, fetch the default
            if ( $foundSocket === null ) {
                $foundSocket = Socket::where([
                    'wrench_id' => $wrench->id,
                    'is_default_socket' => 1
                ])->first();
            }
        } else {
            throw new \Exception("Zermelo SocketWrench Error: No Wrench found for lookup string=`$key`");
        }

        return $foundSocket;
    }

    /**
     * Check the default socket in sockets table to make sure that
     * each wrench as one, and only one, default socket
     */
    public static function checkIsDefaultSocket()
    {
        // Fetch all the sockets
        $sockets = Socket::all();

        // Count the default sockets for each
        $default_hist = [];
        foreach ($sockets as $socket) {

	    if(is_object($socket->wrench)){ //to account for cases where we have sockets without wrenches
            	if (!isset($default_hist[$socket->wrench->wrench_label])) {
                	$default_hist[$socket->wrench->wrench_label] = 0;
            	}
	

     		if ($socket->is_default_socket == 1) {
               		$default_hist[$socket->wrench->wrench_label]++;
            	}
	    }
        }

        $message = "";
        foreach ($default_hist as $label => $count) {
            if ($count > 1) {
                $message.= "Too many default sockets ($count) for Wrench `$label`\n";
            } else if ($count == 0) {
                $message.= "No default socket for Wrench `$label`\n";
            }
        }

	/* This should not interfere with cli operations of php... */

	$is_cli = false;
	if (php_sapi_name() == "cli") {
		$is_cli = true;
	}

        if ($message && $is_cli) {
            throw new \Exception($message);
        }
    }

}
