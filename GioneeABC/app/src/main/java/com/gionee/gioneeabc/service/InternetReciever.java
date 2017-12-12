package com.gionee.gioneeabc.service;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import com.gionee.gioneeabc.helpers.OfflineServiceClass;
import com.gionee.gioneeabc.helpers.Util;

/**
 * Created by Linchpin
 */
public class InternetReciever extends BroadcastReceiver {
    //    private static final int ID_SEND_RETAILER_INFO = 101;
//    private static final int SEARCH_RETAILER_LIST = 102;
//    private Context context;
    private static boolean isDataReceived = false;

    @Override
    public void onReceive(Context context, Intent intent) {
//        this.context = context;
        if (Util.isNetworkAvailable(context)) {
            if (!isDataReceived) {
                isDataReceived = true;
                OfflineServiceClass.getInstance(context).updateAuditTrailDataOnServer();
                OfflineServiceClass.getInstance(context).readUpdateStatusOnServer();
                OfflineServiceClass.getInstance(context).onHitLogoutWebService(false);
            }
        } else {
            isDataReceived = false;
        }
    }
}
