package com.gionee.gioneeabc.activities;

import android.app.ProgressDialog;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkTask;

import java.net.URLEncoder;

/**
 * Created by Linchpin25 on 5/5/2016.
 */
public class TrainingActivity extends ActionBarActivity implements NetworkTask.Result {
    WebView wvTraining;

    ProgressDialog progressBar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.training_activity);
        wvTraining = (WebView) findViewById(R.id.wvTraining);

        WebSettings settings = wvTraining.getSettings();
        settings.setJavaScriptEnabled(true);
        wvTraining.setScrollBarStyle(WebView.SCROLLBARS_OUTSIDE_OVERLAY);
        wvTraining.setWebViewClient(new WebViewClient());
        StringBuffer buffer = new StringBuffer("http://192.168.11.229/learning/login/index1.php");
        buffer.append("?username=" + URLEncoder.encode(DataStore.getEmail(this, DataStore.USER_EMAIL)));
        buffer.append("&password=" + URLEncoder.encode(DataStore.getPass(this, DataStore.USER_PASS)));
        wvTraining.loadUrl(buffer.toString());


    }

    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {

        }


    }
}
