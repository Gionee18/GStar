package com.gionee.gioneeabc.service;

import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.support.v4.app.NotificationCompat;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.MainActivity;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;

import java.util.Random;

/**
 * Created by admin on 02-12-2016.
 */
public class MyFirebaseMessagingService extends FirebaseMessagingService {

    //    private static final String TAG = "MyFirebaseMsgService";
    private ImageLoader imageLoader;
    private DisplayImageOptions options;

    private String title;
    private String body, category, subCategory;
    private String click_action;
    private Context mContext;

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        //Displaying data in log
        //It is optional
        mContext = MyFirebaseMessagingService.this;
        title = remoteMessage.getData().get("title");
        body = remoteMessage.getData().get("body");
        click_action = remoteMessage.getData().get("click_action");
        if (click_action.equalsIgnoreCase("UpdateActivity")) {
            category = remoteMessage.getData().get("category");
            subCategory = remoteMessage.getData().get("subcategory");
        }
        sendNotification();
    }


    private void sendNotification() {
        Random random = new Random();
        int m = random.nextInt(9999 - 1000) + 1000;
        Intent intent = null;

        if (click_action.equalsIgnoreCase("UpdateActivity")) {
            intent = new Intent(MyFirebaseMessagingService.this, MainActivity.class);
            intent.putExtra("type", "push");
            intent.putExtra("topic", body);
            intent.putExtra("category", category);
            intent.putExtra("subcategory", subCategory);
            intent.putExtra("NavigationActivity", "UpdateActivity");
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        } else {
            intent = new Intent(Intent.ACTION_VIEW);
            intent.setData(Uri.parse(body));
        }

        PendingIntent pendingIntent = PendingIntent.getActivity(MyFirebaseMessagingService.this, m, intent, 0);
        NotificationManager notificationManager = (NotificationManager) mContext.getSystemService(Context.NOTIFICATION_SERVICE);


        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(
                this)
                .setSmallIcon(getNotificationIcon())
                .setContentTitle(title)
                .setContentIntent(pendingIntent)
                .setStyle(new NotificationCompat.BigTextStyle().bigText(body))
                .setContentText(body)
                .setAutoCancel(true);

        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.LOLLIPOP) {
            mBuilder.setColor(Color.parseColor("#ED6708"));
        }
        notificationManager.notify(m, mBuilder.build());


    }


    private int getNotificationIcon() {
        boolean useWhiteIcon = (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.LOLLIPOP);
        return useWhiteIcon ? R.mipmap.ic_launcher : R.mipmap.ic_launcher;


    }


}