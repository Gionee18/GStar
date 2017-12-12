package com.gionee.gioneeabc.service;

import com.gionee.gioneeabc.helpers.DataStore;
import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.FirebaseInstanceIdService;

/**
 * Created by admin on 02-12-2016.
 */
public class MyFirebaseInstanceIDService extends FirebaseInstanceIdService {

//    private static final String TAG = "MyFirebaseIIDService";

    @Override
    public void onTokenRefresh() {

        //Getting registration token
        String refreshedToken = FirebaseInstanceId.getInstance().getToken();
//        String android_id = Settings.Secure.getString(this.getContentResolver(), Settings.Secure.ANDROID_ID);

        if (refreshedToken != null && !refreshedToken.equalsIgnoreCase("")) {
            DataStore.setFCMToken(this,refreshedToken);
            DataStore.setIsFCMTokenUpdate(this,false);
        }

    }
}