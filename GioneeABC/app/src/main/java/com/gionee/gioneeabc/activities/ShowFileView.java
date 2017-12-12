package com.gionee.gioneeabc.activities;

import android.app.Activity;
import android.app.DownloadManager;
import android.content.ActivityNotFoundException;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.util.Log;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.Toast;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.bean.DocumentBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.Util;

import java.io.File;
import java.util.HashMap;

public class ShowFileView extends Activity {
    private String url = "";
    private DocumentBean document;
    private long enqueue;
    private DownloadManager downloadmanager;
    private HashMap<Long, Integer> hm;
    private DataBaseHandler dbHandler;
    private WebView webview;
    private BroadcastReceiver catImagereceiver1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.e("MyLog", "onCreate");
        setContentView(R.layout.show_file_view);
        webview = (WebView) findViewById(R.id.webview);
        WebSettings settings = webview.getSettings();
        webview.clearCache(true);
        settings.setUseWideViewPort(true);
        settings.setLoadWithOverviewMode(true);
        settings.setDisplayZoomControls(true);
        webview.setWebChromeClient(new WebChromeClient());
        webview.getSettings().setJavaScriptEnabled(true);
        webview.getSettings().setJavaScriptCanOpenWindowsAutomatically(true);
        webview.getSettings().setPluginState(WebSettings.PluginState.ON);

        hm = new HashMap<Long, Integer>();
        downloadmanager = (DownloadManager) getSystemService(Context.DOWNLOAD_SERVICE);
        dbHandler = new DataBaseHandler(this);
        if (getIntent() != null) {
            document = (DocumentBean) getIntent().getSerializableExtra("document");
            url = NetworkConstants.BASE_URL + "/" + document.getDocUrl() + "/" + document.getDocName();
            //url="http://mikul262.webs.com/3755215-You-Can-Win-by-Shiv-Khera.pdf";
            // webview.loadUrl("https://docs.google.com/viewer?url="+url);
        }
        catImagereceiver1 = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String action = intent.getAction();
                if (DownloadManager.ACTION_DOWNLOAD_COMPLETE.equals(action)) {
                    long downloadId = intent.getLongExtra(
                            DownloadManager.EXTRA_DOWNLOAD_ID, 0);
                    DownloadManager.Query query = new DownloadManager.Query();
                    query.setFilterById(enqueue);
                    Cursor c = downloadmanager.query(query);
                    if (c.moveToFirst()) {
                        int columnIndex = c
                                .getColumnIndex(DownloadManager.COLUMN_STATUS);
                        if (DownloadManager.STATUS_SUCCESSFUL == c.getInt(columnIndex)) {
                            final int pos = hm.get(enqueue);
                            dbHandler.updateDocumentLocalPath(document.getDocId(), Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" +
                                    NetworkConstants.hideImageFromGallery + document.getDocName());
                            document.setDocLocalPath(Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar/" + document.getDocName());
                            openFileIntent(document);
                        }
                    }
                }
            }
        };
        switch (document.getDocType()) {
            case "3GP":
            case "MP4":
            case "JPG":
            case "PNG":
            case "GIF":
                if (Util.isNetworkAvailable(this))
                    webview.loadUrl(url);
                else
                    Toast.makeText(this, "Please check your network connection", Toast.LENGTH_SHORT).show();
                break;
            case "TXT":
            case "XLS":
            case "XLSX":
            case "PDF":
            case "DOC":
            case "DOCX":
                if (Util.isNetworkAvailable(this))
                    fileDownload(0);
                else
                    Toast.makeText(this, "Please check your network connection", Toast.LENGTH_SHORT).show();
                break;
        }
        registerReceiver(catImagereceiver1, new IntentFilter(
                DownloadManager.ACTION_DOWNLOAD_COMPLETE));
    }

    public void fileDownload(int i) {
        File direct = new File(Environment.getExternalStorageDirectory()
                + NetworkConstants.hideFolderFromGallery + "GioneeStar");
        if (!direct.exists()) {
            direct.mkdirs();
        }
        try {
            url = NetworkConstants.BASE_URL + "/" + document.getDocUrl() + "/" + document.getDocName();
            Uri downloadUri = Uri.parse(url);
            DownloadManager.Request request = new DownloadManager.Request(
                    downloadUri);
            request.setAllowedNetworkTypes(
                    DownloadManager.Request.NETWORK_WIFI
                            | DownloadManager.Request.NETWORK_MOBILE)
                    .setAllowedOverRoaming(false)
                    .setTitle("GioneeStar")
                    .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery + "GioneeStar", document.getDocName());
            enqueue = downloadmanager.enqueue(request);
            hm.put(enqueue, i);
            Util.createToast(ShowFileView.this, "Opening...");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }


    private void openFileIntent(DocumentBean doc) {
        File file = new File(doc.getDocLocalPath());
        Uri path = Uri.fromFile(file);
        Intent fileOpenintent = null;
        switch (doc.getDocType()) {
            case "PDF":
            case "DOCX":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/pdf");
                finish();
                break;
            case "TXT":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/msword");
                finish();
                break;
            case "DOC":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/msword");
                finish();
                break;
            case "XLS":
            case "XLSX":
                fileOpenintent = new Intent(Intent.ACTION_VIEW);
                fileOpenintent.setDataAndType(path, "application/vnd.ms-excel");
                finish();
                break;
            case "JPG":
            case "GIF":
                startActivity(new Intent(this, ImageViewActivity.class).putExtra("imagePath", doc.getDocLocalPath()));
                finish();
                break;
            case "PNG":
                startActivity(new Intent(this, ImageViewActivity.class).putExtra("imagePath", doc.getDocLocalPath()));
                finish();
                break;
        }
        if (fileOpenintent != null) {
            fileOpenintent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            try {
                startActivity(fileOpenintent);
            } catch (ActivityNotFoundException e) {
                e.printStackTrace();

            }
        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        unregisterReceiver(catImagereceiver1);
    }

    @Override
    protected void onPause() {
        super.onPause();
        if (webview!=null)
            webview.loadUrl("");
    }
}
