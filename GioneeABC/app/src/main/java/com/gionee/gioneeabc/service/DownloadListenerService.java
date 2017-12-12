package com.gionee.gioneeabc.service;

import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;

import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.Util;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

/**
 * Created by Linchpin
 */
public class DownloadListenerService extends BroadcastReceiver {
    @Override
    public void onReceive(Context context, Intent intent) {
        String action = intent.getAction();
        if (DownloadManager.ACTION_NOTIFICATION_CLICKED.equals(action)) {
            Intent i = new Intent(DownloadManager.ACTION_VIEW_DOWNLOADS);
            i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            context.startActivity(i);
        } else if (DownloadManager.ACTION_DOWNLOAD_COMPLETE.equals(action)) {
            Bundle extras = intent.getExtras();
            DownloadManager.Query q = new DownloadManager.Query();
            long enqueue=extras.getLong(DownloadManager.EXTRA_DOWNLOAD_ID);
            String videoQueueIds = DataStore.getVideoQueueIds(context);
            if (videoQueueIds.contains("" + enqueue)) {
                q.setFilterById(enqueue);
                DownloadManager downloadmanager = (DownloadManager) context.getSystemService(Context.DOWNLOAD_SERVICE);
                Cursor c = downloadmanager.query(q);

                if (c.moveToFirst()) {
                    int status = c.getInt(c.getColumnIndex(DownloadManager.COLUMN_STATUS));
                    if (status == DownloadManager.STATUS_SUCCESSFUL) {
//                        String filePath = c.getString(c.getColumnIndex(DownloadManager.COLUMN_LOCAL_FILENAME));
//                        String filename = filePath.substring(filePath.lastIndexOf('/') + 1, filePath.length());
                        Util.createToast(context, "Download completed successfully");
                        deleteVideoId(context, videoQueueIds, "" + enqueue);
                    }else if (status==DownloadManager.STATUS_FAILED) {
                        Util.createToast(context, "Download failed");
                        deleteVideoId(context, videoQueueIds, "" + enqueue);
                        downloadmanager.remove(enqueue);
                    } else if (status==DownloadManager.STATUS_PAUSED) {
                        Util.createToast(context, "Download paused");
                        deleteVideoId(context, videoQueueIds, "" + enqueue);
                        downloadmanager.remove(enqueue);
                    } else if (status==DownloadManager.STATUS_PENDING) {
                        Util.createToast(context, "Download pending");
                        deleteVideoId(context, videoQueueIds, "" + enqueue);
                        downloadmanager.remove(enqueue);
                    } else if (status==DownloadManager.STATUS_RUNNING) {
                        Util.createToast(context, "Downloading is going on");
                    }
                }
                c.close();
            }
            /*DownloadManager.Query query = new DownloadManager.Query();
            String videoQueueIds = DataStore.getVideoQueueIds(context);
            if (!videoQueueIds.isEmpty()) {
                String[] videoIds = videoQueueIds.split(",");
                DownloadManager downloadmanager = (DownloadManager) context.getSystemService(Context.DOWNLOAD_SERVICE);
                for (int i = 0; i < videoIds.length; i++) {
                    query.setFilterById(Long.parseLong(videoIds[i]));
                    Cursor c = downloadmanager.query(query);
                    if (c.moveToFirst()) {
                        int columnIndex = c
                                .getColumnIndex(DownloadManager.COLUMN_STATUS);
                        if (DownloadManager.STATUS_SUCCESSFUL == c.getInt(columnIndex)) {
                            Util.createToast(context, "STATUS_SUCCESSFUL");
                            deleteVideoId(context, videoQueueIds, videoIds[i]);

//                            final int pos = hm.get(enqueue);
//                            openDialog(tutorialResponseBean.getData().get(catogarySelectedPage).getProduct().get(productSelectePage).getTutorials().getVideo().get(pos));
                        } else if (DownloadManager.STATUS_FAILED == c.getInt(columnIndex)) {
                            Util.createToast(context, "STATUS_FAILED");
                            deleteVideoId(context, videoQueueIds, videoIds[i]);
                            downloadmanager.remove(Long.parseLong(videoIds[i]));
                        } else if (DownloadManager.STATUS_PAUSED == c.getInt(columnIndex)) {
                            Util.createToast(context, "STATUS_PAUSED");
                            deleteVideoId(context, videoQueueIds, videoIds[i]);
                            downloadmanager.remove(Long.parseLong(videoIds[i]));
                        } else if (DownloadManager.STATUS_PENDING == c.getInt(columnIndex)) {
                            Util.createToast(context, "STATUS_PENDING");
                            deleteVideoId(context, videoQueueIds, videoIds[i]);
                            downloadmanager.remove(Long.parseLong(videoIds[i]));
                        } else if (DownloadManager.STATUS_RUNNING == c.getInt(columnIndex)) {
                            Util.createToast(context, "STATUS_RUNNING");
                        }
                    }
                }

            }*/
        }
    }

    private void deleteVideoId(Context context, String videoQueueIds, String videoId) {
        String[] videoIds1 = videoQueueIds.split(",");
        List<String> list = new ArrayList<String>(Arrays.asList(videoIds1));
        list.remove(videoId);
        videoIds1 = list.toArray(new String[0]);
        if (videoIds1.length > 0) {
            String id = "";
            for (int i = 0; i < videoIds1.length; i++) {
                if (i == 0)
                    id = id + videoIds1[i];
                else
                    id = id + "," + videoIds1[i];
            }
            DataStore.setVideoQueueIds(context, id);
        } else {
            DataStore.setVideoQueueIds(context, "");
        }
    }
}
