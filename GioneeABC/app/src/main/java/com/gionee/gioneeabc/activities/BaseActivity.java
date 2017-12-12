package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.gionee.gioneeabc.helpers.Util;

/**
 * Created by Linchpin
 */
public class BaseActivity extends AppCompatActivity {

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        new MenuInflater(getApplication()).inflate(R.menu.menu_all, menu);
        return true;
    }


    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.ic_product) {
            Intent intent=new Intent(this, ProductsActivity.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
            startActivity(intent);

           /* ActivityManager m = (ActivityManager) getSystemService(ACTIVITY_SERVICE);
            List<ActivityManager.RunningTaskInfo> runningTaskInfoList =  m.getRunningTasks(10);
            Iterator<ActivityManager.RunningTaskInfo> itr = runningTaskInfoList.iterator();
            while(itr.hasNext()){
                ActivityManager.RunningTaskInfo runningTaskInfo = (ActivityManager.RunningTaskInfo)itr.next();
                int numOfActivities = runningTaskInfo.numActivities;
                String baseActivity=runningTaskInfo.baseActivity.getShortClassName();
                if (baseActivity.equalsIgnoreCase(".activities.MainActivity")){
                    for (int i=0;i<numOfActivities-1;i++){
                        finish();
                    }
                }
            }*/
        }else if (id==R.id.ic_tutorial){
            Intent intent=new Intent(this, TutorialProductsActivity.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
            startActivity(intent);
        }else if (id==R.id.ic_updates){
            Intent intent=new Intent(this, UpdateActivity.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
            startActivity(intent);
        }else if (id==R.id.ic_recomm){
            UIUtils.isFilterFromProduct=false;
            Intent intent = new Intent(this, RecomenderActivity.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
            intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER);
            if (Util.isBrandModelSelected(this))
                intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_FILTER);
            else
                intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_MAIN);
            startActivity(intent);
        }else if (id==R.id.ic_home){
            Intent intent=new Intent(this, MainActivity.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
            startActivity(intent);
        }
        return super.onOptionsItemSelected(item);
    }
}
