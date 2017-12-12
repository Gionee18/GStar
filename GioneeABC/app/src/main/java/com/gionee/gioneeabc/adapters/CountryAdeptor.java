package com.gionee.gioneeabc.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.gionee.gioneeabc.R;

import java.util.ArrayList;

/**
 * Created by admin on 17-10-2016.
 */
public class CountryAdeptor extends BaseAdapter {
    ArrayList<String> countryList = null;
    Context context;

    //
    public CountryAdeptor(ArrayList<String> countryList, Context context) {
        this.countryList = countryList;
        this.context = context;
        //
    }

    public void init(ArrayList<String> country) {
        this.countryList = country;
    }

    @Override
    public int getCount() {
        // TODO Auto-generated method stub
        return countryList.size();
    }

    @Override
    public Object getItem(int arg0) {
        // TODO Auto-generated method stub
        return null;
    }

    @Override
    public long getItemId(int arg0) {
        // TODO Auto-generated method stub
        return 0;
    }

    @Override
    public View getView(int pos, View contentView, ViewGroup arg2) {
        CahceHolder cache = null;
        if (null == contentView) {
            LayoutInflater inflator = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            contentView = inflator.inflate(R.layout.label_name, null);
            cache = new CahceHolder(contentView);
            contentView.setTag(cache);
        } else {
            cache = (CahceHolder) contentView.getTag();
        }
        // setFontForTextView(cache.tv_Name);
        cache.tv_Name.setText(countryList.get(pos));
        return contentView;
    }

    public static class CahceHolder {
        private TextView tv_Name;

        CahceHolder(View view) {

            tv_Name = (TextView) view.findViewById(R.id.tv_Name);

        }
    }

}

