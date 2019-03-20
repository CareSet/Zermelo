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
     */
    public function setSocketsFromApiInput( $input )
    {
        // Set the sockets for the current report, and save if we have a user
        $user = Auth::user();
        if ( $user !== null ) {
            $userId = $user->id;
            // First delete our old settings
            SocketUser::where('user_id', $userId)->delete();
            foreach ( $input as $setting ) {
                // Save the user's setting
                $socketUser = new SocketUser([
                    'user_id' => $userId,
                    'wrench_id' => $setting['wrenchId'],
                    'current_chosen_socket' => $setting['socketId']
                ]);
                $socketUser->save();
            }
        }

        // Now set the active sockets for the view (we do this even if there's no user for the current request)
        foreach ( $input as $setting ) {
            $socket = Socket::find( $setting[ 'socketId' ] );
            $this->activeSockets[] = $socket;
        }

        return $this->activeSockets;
    }

    public function fetchSocketForWrenchKey( $key )
    {
        $wrench = Wrench::where( 'wrench_lookup_string', $key )->first();
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

        return $foundSocket;
    }

}
