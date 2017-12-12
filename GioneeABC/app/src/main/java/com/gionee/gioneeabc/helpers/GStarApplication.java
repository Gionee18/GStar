package com.gionee.gioneeabc.helpers;

import android.app.Application;

import com.google.android.gms.analytics.GoogleAnalytics;
import com.google.android.gms.analytics.HitBuilders;
import com.google.android.gms.analytics.StandardExceptionParser;
import com.google.android.gms.analytics.Tracker;

/**
 * Created by Linchpin25 on 1/4/2016.
 */
public class GStarApplication extends Application
{
    public static final String TAG = GStarApplication.class
            .getSimpleName();
    private static GStarApplication mInstance;

    @Override
    public void onCreate() {
        super.onCreate();
        mInstance = this;

        AnalyticsTracker.initialize(this);
        AnalyticsTracker.getInstance().get(AnalyticsTracker.Target.APP);
    }

    public static synchronized GStarApplication getInstance() {
        return mInstance;
    }
    public synchronized Tracker getGoogleAnalyticsTracker() {
        AnalyticsTracker analyticsTrackers = AnalyticsTracker.getInstance();
        return analyticsTrackers.get(AnalyticsTracker.Target.APP);
    }
    public void trackScreenView(String screenName) {
        Tracker t = getGoogleAnalyticsTracker();

        // Set screen name.
        t.setScreenName(screenName);



        // Send a screen view.
        t.send(new HitBuilders.ScreenViewBuilder().build());

        GoogleAnalytics.getInstance(this).dispatchLocalHits();
    }

    /***
     * Tracking exception
     *
     * @param e exception to be tracked
     */
    public void trackException(Exception e) {
        if (e != null) {
            Tracker t = getGoogleAnalyticsTracker();

            t.send(new HitBuilders.ExceptionBuilder()
                            .setDescription(
                                    new StandardExceptionParser(this, null)
                                            .getDescription(Thread.currentThread().getName(), e))
                            .setFatal(false)
                            .build()
            );
        }
    }


}
