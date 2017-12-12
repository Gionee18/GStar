package com.gionee.gioneeabc.activities;

import android.os.Bundle;
import android.support.v4.view.ViewPager;
import android.support.v7.app.AppCompatActivity;

import com.gionee.gioneeabc.R;

/**
 * Created by Linchpin25 on 2/24/2016.
 */
public class PhoneImageActivity extends AppCompatActivity {
    ViewPager viewPager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.phone_image_activity);
        viewPager = (ViewPager) findViewById(R.id.viewPager);






    }
}
