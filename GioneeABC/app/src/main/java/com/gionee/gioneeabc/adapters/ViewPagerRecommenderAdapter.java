package com.gionee.gioneeabc.adapters;

import android.app.Activity;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.fragments.CompareSpecificationFragment;
import com.gionee.gioneeabc.fragments.RecommProductListFragment;
import com.gionee.gioneeabc.fragments.RecommenderListFragment;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.gionee.gioneeabc.helpers.Util;

/**
 * Created by Linchpin
 */
public class ViewPagerRecommenderAdapter extends FragmentStatePagerAdapter {
    private String[] tabList;
    private String from;
    private Activity activity;
    private boolean isManufacturer;
    private String gionee_id;

    public ViewPagerRecommenderAdapter(Activity activity, FragmentManager supportFragmentManager, String from,
                                       boolean isManufacturer, String gionee_id) {
        super(supportFragmentManager);
        this.activity = activity;
        this.from = from;
        this.isManufacturer = isManufacturer;
        this.gionee_id = gionee_id;
        tabList = new String[]{activity.getString(R.string.text_filter_by_manufacturer), activity.getString(R.string.text_filter_by_atributes), activity.getString(R.string.title_compare)};
    }

    @Override
    public Fragment getItem(int position) {
        Fragment fragment = null;
        /*if (from.equalsIgnoreCase("main")) {
            fragment = new RecommenderListFragment();
        } else {
            fragment = new RecommProductListFragment();
        }*/

        if (position == 0) {
            if (Util.isBrandModelSelected(activity)) {
//                fragment = new RecommProductListFragment();
                if (from!=null && from.equalsIgnoreCase("main") && isManufacturer) {
                    fragment = new RecommenderListFragment();
                } else {
                    fragment = new RecommProductListFragment();
                }
            } else
                fragment = new RecommenderListFragment();
        } else if (position == 1) {
            if (Util.isAttribSelected(activity)) {
//                fragment = new RecommProductListFragment();
                if (from!=null && from.equalsIgnoreCase("main") && !isManufacturer) {
                    fragment = new RecommenderListFragment();
                } else {
                    fragment = new RecommProductListFragment();
                }
            } else {
//                fragment = new RecommenderListFragment();
                if (from!=null && from.equalsIgnoreCase("filter") && !isManufacturer) {
                    fragment = new RecommProductListFragment();
                } else {
                    fragment = new RecommenderListFragment();
                }
            }

        } else if (position == 2) {
            fragment = new CompareSpecificationFragment();
        }

        Bundle bundle = new Bundle();
        if (position == 0) {
            bundle.putString(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER);
        } else if (position == 1) {
            bundle.putString(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_ATTRIB);
        } else if (position == 2) {
            if (gionee_id != null)
                bundle.putString("gionee_id", gionee_id);
        }
        fragment.setArguments(bundle);

        return fragment;
    }

    @Override
    public CharSequence getPageTitle(int position) {
        return tabList[position];
    }

    @Override
    public int getItemPosition(Object object) {
        // Causes adapter to reload all Fragments when
        // notifyDataSetChanged is called
        return POSITION_NONE;
    }

    @Override
    public int getCount() {
        return tabList.length;
    }
}
