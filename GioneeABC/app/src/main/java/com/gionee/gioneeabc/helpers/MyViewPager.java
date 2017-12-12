package com.gionee.gioneeabc.helpers;

import android.content.Context;
import android.support.v4.view.ViewPager;
import android.view.MotionEvent;

public class MyViewPager extends ViewPager {
    private boolean pagingEnabled = true;

    public MyViewPager(Context context) {
        super(context);
    }

    /* constructors omitted */

    public void setPagingEnabled(boolean enabled) {
        pagingEnabled = enabled;
    }

    @Override
    public boolean onInterceptTouchEvent(MotionEvent event) {
        if (!pagingEnabled) {
            return false; // do not intercept
        }
        return super.onInterceptTouchEvent(event);
    }

    @Override
    public boolean onTouchEvent(MotionEvent event) {
        if (!pagingEnabled) {
            return false; // do not consume
        }
        return super.onTouchEvent(event);
    }
}