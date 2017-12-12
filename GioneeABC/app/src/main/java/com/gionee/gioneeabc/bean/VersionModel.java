package com.gionee.gioneeabc.bean;

/**
 * Created by Linchpin25 on 1/22/2016.
 */
public class VersionModel
{
    public String name;

    public static final String[] data = {"Cupcake", "Donut", "Eclair",
            "Froyo", "Gingerbread", "Honeycomb",
            "Icecream Sandwich", "Jelly Bean", "Kitkat", "Lollipop"};

    VersionModel(String name){
        this.name=name;
    }
}
