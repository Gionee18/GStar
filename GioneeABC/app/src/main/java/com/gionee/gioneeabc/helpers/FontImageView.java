package com.gionee.gioneeabc.helpers;

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.TextView;

/**
 * Created by Linchpin25 on 10/23/2015.
 */
public class FontImageView extends TextView {
    public FontImageView(Context context) {
        super(context);
        init(context);
    }

    public FontImageView(Context context, AttributeSet attrs) {
        super(context, attrs);
        init(context);
    }

    public FontImageView(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        init(context);
    }

    private void init(Context con) {
        Typeface tf = Typeface.createFromAsset(con.getAssets(), "font/gStar.ttf");
        setTypeface(tf);
    }


}
