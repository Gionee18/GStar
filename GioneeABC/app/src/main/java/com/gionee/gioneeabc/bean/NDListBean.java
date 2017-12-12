package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by admin on 17-10-2016.
 */
public class NDListBean {
    private int count;

    public int getCount() {
        return this.count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    private String status;

    public String getStatus() {
        return this.status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    private ArrayList<String> data;

    public ArrayList<String> getData() {
        return this.data;
    }

    public void setData(ArrayList<String> data) {
        this.data = data;
    }
}
