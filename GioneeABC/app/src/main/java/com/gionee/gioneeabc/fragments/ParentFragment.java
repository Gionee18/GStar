package com.gionee.gioneeabc.fragments;

import android.app.Activity;
import android.support.v4.app.Fragment;

import com.gionee.gioneeabc.interfaces.IMessenger;


public class ParentFragment extends Fragment {

    protected IMessenger activityCallback;

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);
        try {
            activityCallback = (IMessenger) activity;
        } catch (ClassCastException e) {
            throw new ClassCastException(activity.toString()
                    + " must implement ToolbarListener");
        }
    }
}
