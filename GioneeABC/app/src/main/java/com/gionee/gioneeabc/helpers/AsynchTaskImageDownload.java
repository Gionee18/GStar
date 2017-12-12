package com.gionee.gioneeabc.helpers;

import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Environment;

import com.gionee.gioneeabc.bean.ImageBean;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URL;
import java.util.HashMap;
import java.util.List;

/**
 * Created by lptpl on 26/4/16.
 */
public class AsynchTaskImageDownload extends AsyncTask {
    private List<ImageBean> downloadEachImage;
    private Context context;
    private DownloadManager downloadmanager;
    private BroadcastReceiver imagereceiver;
    private long enqueue;
    HashMap<Long, Integer> hm;

    public AsynchTaskImageDownload(Context context, List<ImageBean> downloadEachImage) {
        this.context = context;
        this.downloadEachImage = downloadEachImage;
    }

    @Override
    protected Object doInBackground(Object[] params) {
        for (ImageBean imagebean : downloadEachImage)
            fileDownload(imagebean);
        return null;
    }

    @Override
    protected void onPostExecute(Object o) {

        super.onPostExecute(o);
    }

    public void fileDownload(ImageBean imageBean) {
        try {
            File direct = new File(Environment.getExternalStorageDirectory()
                    + NetworkConstants.hideFolderFromGallery + "GioneeStar");
            if (!direct.exists()) {
                direct.mkdirs();
            }
            String str_url = NetworkConstants.BASE_URL + imageBean.getImageServerPath() + "/" + imageBean.getImageName();
            URL url = new URL(str_url);
            InputStream input = url.openStream();
            //The sdcard directory e.g. '/sdcard' can be used directly, or
            //more safely abstracted with getExternalStorageDirectory()
            String saveLocation = Environment.getExternalStorageDirectory() + NetworkConstants.hideFolderFromGallery + "GioneeStar";
            // File storagePath = Environment.getExternalStorageDirectory();
            OutputStream output = new FileOutputStream(new File(saveLocation, imageBean.getImageName()));
            byte[] buffer = new byte[1024];
            int bytesRead = 0;
            while ((bytesRead = input.read(buffer, 0, buffer.length)) >= 0) {
                output.write(buffer, 0, bytesRead);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
