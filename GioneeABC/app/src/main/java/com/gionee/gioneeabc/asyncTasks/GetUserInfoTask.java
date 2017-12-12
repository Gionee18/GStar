package com.gionee.gioneeabc.asyncTasks;

import android.content.Context;
import android.os.AsyncTask;

import com.gionee.gioneeabc.helpers.DataStore;

/**
 * Created by Linchpin25 on 3/17/2016.
 */
public class GetUserInfoTask extends AsyncTask<Void, Void, Void> {
    Context mContext;
    String response;
    String authToken;

    public GetUserInfoTask(Context mContext) {
        this.mContext = mContext;
        authToken = DataStore.getAuthToken(mContext, DataStore.AUTH_TOKEN);

    }

    @Override
    protected Void doInBackground(Void... params) {
        return null;
    }

    @Override
    protected void onPostExecute(Void aVoid) {
        super.onPostExecute(aVoid);


    }
}
