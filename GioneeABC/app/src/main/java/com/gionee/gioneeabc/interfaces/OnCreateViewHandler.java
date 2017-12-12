package com.gionee.gioneeabc.interfaces;

import android.os.Bundle;

/**
 * Created by linchpin11192 on 01-May-2016.
 */
public interface OnCreateViewHandler {
    int getLayoutResId();

    void initializeViews(Bundle savedInstanceState);

    void setDataOnViews(Bundle savedInstanceState);

    void handleViewsVisibility(Bundle savedInstanceState);

    void setListenersOnViews(Bundle savedInstanceState);
}