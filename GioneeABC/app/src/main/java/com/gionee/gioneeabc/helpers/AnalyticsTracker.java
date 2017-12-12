package com.gionee.gioneeabc.helpers;

import android.content.Context;

import com.gionee.gioneeabc.R;
import com.google.android.gms.analytics.GoogleAnalytics;
import com.google.android.gms.analytics.Tracker;

import java.util.HashMap;
import java.util.Map;

/**
 * Created by Linchpin25 on 1/4/2016.
 */
public final class AnalyticsTracker
{
    public enum Target {APP};
    private static AnalyticsTracker sInstance;
    private final Context mContext;
    private final Map<Target, Tracker> mTrackers = new HashMap<Target, Tracker>();

    public static synchronized void initialize(Context context) {
        if (sInstance != null) {
            throw new IllegalStateException("Extra call to initialize analytics trackers");
        }

        sInstance = new AnalyticsTracker(context);
    }


    /**
     * Don't instantiate directly - use {getInstance()} instead.
     */
    private AnalyticsTracker(Context context) {
        mContext = context.getApplicationContext();
    }

    public synchronized Tracker get(Target target) {
        if (!mTrackers.containsKey(target)) {
            Tracker tracker;
            switch (target) {
                case APP:
                    tracker = GoogleAnalytics.getInstance(mContext).newTracker(R.xml.app_tracker);
                    break;
                default:
                    throw new IllegalArgumentException("Unhandled analytics target " + target);
            }
            mTrackers.put(target, tracker);
        }

        return mTrackers.get(target);
    }

    public static synchronized AnalyticsTracker getInstance()
    {
        if (sInstance == null) {
            throw new IllegalStateException("Call initialize() before getInstance()");
        }

        return sInstance;
    }



}
