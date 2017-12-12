package com.gionee.gioneeabc.fragments;


import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.gionee.gioneeabc.interfaces.OnCreateViewHandler;

/**
 * A simple {@link Fragment} subclass.
 */
public abstract class UtilityFragment extends Fragment implements OnCreateViewHandler {

    View fView;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
//        setHasOptionsMenu(true);
        if (fView == null) {
            fView = inflater.inflate(getLayoutResId(), container, false);
            initializeViews(savedInstanceState);
            handleViewsVisibility(savedInstanceState);
            setDataOnViews(savedInstanceState);
            setListenersOnViews(savedInstanceState);
        }
        return fView;
    }

    /*@Override
    public void onPrepareOptionsMenu(Menu menu) {
        MenuItem itemS = menu.findItem(R.id.edit_profile);
        MenuItem itemS1 = menu.findItem(R.id.change_password);
        MenuItem itemS2 = menu.findItem(R.id.update);
        itemS.setVisible(false);
        itemS1.setVisible(false);
        itemS2.setVisible(false);
        super.onPrepareOptionsMenu(menu);
    }*/

}
