package com.gionee.gioneeabc.activities;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.graphics.PointF;
import android.os.Bundle;
import android.view.MotionEvent;
import android.view.Window;
import android.view.WindowManager;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.helpers.CustomImageView;
import com.squareup.picasso.Picasso;

import java.io.File;

/**
 * Created by Linchpin25 on 3/16/2016.
 */
public class ImageViewActivity extends Activity {

    CustomImageView ivImage;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        requestWindowFeature(Window.FEATURE_NO_TITLE);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN);

        setContentView(R.layout.image_view_activity);

        String imagePath = getIntent().getStringExtra("imagePath");


        ivImage = (CustomImageView) findViewById(R.id.ivImage);

          Picasso.with(ImageViewActivity.this).load(new File(imagePath)).into(ivImage);
    }


    @SuppressLint("FloatMath")
    private float spacing(MotionEvent event) {
        float x = event.getX(0) - event.getX(1);
        float y = event.getY(0) - event.getY(1);
        return (float) (Math.sqrt(x * x + y * y));
    }

    private void midPoint(PointF point, MotionEvent event) {
        float x = event.getX(0) + event.getX(1);
        float y = event.getY(0) + event.getY(1);
        point.set(x / 2, y / 2);
    }


}
